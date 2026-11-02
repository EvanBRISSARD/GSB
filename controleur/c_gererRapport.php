<?php

// Accès réservé aux délégués ou responsables de secteur
if (empty($_SESSION['login'])) {
    include("vues/v_accesInterdit.php");
    exit;
}

include_once __DIR__ . '/../modele/praticien.modele.inc.php';
include_once __DIR__ . '/../modele/rapport.modele.inc.php';

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "connexion";
} else {
    $action = $_REQUEST['action'];
}

switch ($action) {
    case 'formrapportdevisite': {
        $praticiens = getAllNomPraticien();
        $motifs = getAllMotifsVisite();
        $medicaments = getAllMedicamentsPourRapport();
        $formValues = [];
        foreach (['numeroRapport', 'dateVisite', 'motif', 'praticien', 'remplacant', 'bilan', 'saisieDefinitive'] as $field) {
            if (isset($_POST[$field]) && is_scalar($_POST[$field])) {
                $formValues[$field] = (string) $_POST[$field];
            }
        }
        if (isset($_POST['echantillons']) && is_array($_POST['echantillons'])) {
            foreach ($_POST['echantillons'] as $echantillon) {
                if (!is_array($echantillon)) {
                    continue;
                }
                $formValues['echantillons'][] = [
                    'medicament' => isset($echantillon['medicament']) && is_scalar($echantillon['medicament'])
                        ? (string) $echantillon['medicament']
                        : '',
                    'quantite' => isset($echantillon['quantite']) && is_scalar($echantillon['quantite'])
                        ? (string) $echantillon['quantite']
                        : '',
                ];
            }
        }
        $formError = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $postString = static function (string $field): string {
                    $value = $_POST[$field] ?? '';
                    return is_scalar($value) ? trim((string) $value) : '';
                };
                $matricule = (string) ($_SESSION['matricule'] ?? '');
                $numero = filter_var($postString('numeroRapport'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1]
                ]);
                $dateVisite = $postString('dateVisite');
                $dateParts = explode('-', $dateVisite);
                $motif = filter_var($postString('motif'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1]
                ]);
                $praticien = filter_var($postString('praticien'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1]
                ]);
                $remplacantValeur = $postString('remplacant');
                $remplacant = $remplacantValeur === '' ? null : filter_var($remplacantValeur, FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1]
                ]);
                $bilan = $postString('bilan');
                $longueurBilan = preg_match_all('/./us', $bilan);

                if ($matricule === '') {
                    throw new InvalidArgumentException('Votre matricule de collaborateur est introuvable. Veuillez vous reconnecter.');
                }
                if ($numero === false) {
                    throw new InvalidArgumentException('Le numéro de rapport doit être un entier positif.');
                }
                if (
                    preg_match('/^\d{4}-\d{2}-\d{2}$/D', $dateVisite) !== 1
                    || count($dateParts) !== 3
                    || !checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0])
                ) {
                    throw new InvalidArgumentException('Veuillez saisir une date de visite valide.');
                }
                if ($motif === false || !in_array((string) $motif, array_map('strval', array_column($motifs, 'id_motif')), true)) {
                    throw new InvalidArgumentException('Veuillez sélectionner un motif valide.');
                }
                if ($praticien === false || !in_array((string) $praticien, array_map('strval', array_column($praticiens, 'PRA_NUM')), true)) {
                    throw new InvalidArgumentException('Veuillez sélectionner un praticien valide.');
                }
                if ($remplacantValeur !== '' && ($remplacant === false || !in_array((string) $remplacant, array_map('strval', array_column($praticiens, 'PRA_NUM')), true))) {
                    throw new InvalidArgumentException('Veuillez sélectionner un remplaçant valide.');
                }
                if ($bilan === '' || $longueurBilan === false || $longueurBilan > 255) {
                    throw new InvalidArgumentException('Le bilan est obligatoire et ne doit pas dépasser 255 caractères.');
                }

                $codesMedicaments = array_map('strval', array_column($medicaments, 'MED_DEPOTLEGAL'));
                $echantillons = [];
                $codesSelectionnes = [];
                $saisiesEchantillons = $_POST['echantillons'] ?? [];
                if (!is_array($saisiesEchantillons)) {
                    throw new InvalidArgumentException('Les échantillons saisis sont invalides.');
                }
                foreach ($saisiesEchantillons as $echantillon) {
                    if (!is_array($echantillon)) {
                        throw new InvalidArgumentException('Les échantillons saisis sont invalides.');
                    }

                    $codeMedicament = isset($echantillon['medicament']) && is_scalar($echantillon['medicament'])
                        ? trim((string) $echantillon['medicament'])
                        : '';
                    $quantiteValeur = isset($echantillon['quantite']) && is_scalar($echantillon['quantite'])
                        ? trim((string) $echantillon['quantite'])
                        : '';
                    if ($codeMedicament === '' && $quantiteValeur === '') {
                        continue;
                    }
                    if ($codeMedicament === '' || $quantiteValeur === '') {
                        throw new InvalidArgumentException('Pour chaque échantillon, sélectionnez un médicament et indiquez une quantité.');
                    }

                    $quantite = filter_var($quantiteValeur, FILTER_VALIDATE_INT, [
                        'options' => ['min_range' => 1, 'max_range' => 2147483647]
                    ]);
                    if (!in_array($codeMedicament, $codesMedicaments, true) || $quantite === false) {
                        throw new InvalidArgumentException('Chaque échantillon doit contenir un médicament valide et une quantité entière positive.');
                    }
                    if (in_array($codeMedicament, $codesSelectionnes, true)) {
                        throw new InvalidArgumentException('Un même médicament ne peut être saisi qu’une seule fois dans les échantillons.');
                    }

                    $codesSelectionnes[] = $codeMedicament;
                    $echantillons[] = [
                        'medicament' => $codeMedicament,
                        'quantite' => $quantite,
                    ];
                }

                enregistrerRapportVisite([
                    'matricule' => $matricule,
                    'numero' => $numero,
                    'date_visite' => $dateVisite,
                    'bilan' => $bilan,
                    'definitif' => $postString('saisieDefinitive') === '1',
                    'praticien' => $praticien,
                    'motif' => $motif,
                    'remplacant' => $remplacant,
                ], $echantillons);

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'text' => 'Le rapport de visite a été enregistré avec succès.'
                ];
                header('Location: index.php?uc=rapport&action=formrapportdevisite');
                exit;
            } catch (InvalidArgumentException $e) {
                $formError = $e->getMessage();
            } catch (PDOException $e) {
                error_log('Erreur lors de l’enregistrement du rapport de visite : ' . $e->getMessage());
                $formError = 'Le rapport n’a pas pu être enregistré. Vérifiez que son numéro n’est pas déjà utilisé et que les données sont valides.';
            } catch (Throwable $e) {
                error_log('Erreur inattendue lors de l’enregistrement du rapport de visite : ' . $e->getMessage());
                $formError = 'Une erreur est survenue pendant l’enregistrement du rapport.';
            }
        }

        include("vues/v_formRapport.php");
        break;
    }

    default: {
        header("Location: index.php?uc=medicaments&action=FormulaireMedicament");
        break;
    }
}
?>