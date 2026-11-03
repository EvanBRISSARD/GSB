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
    case 'mesrapports': {
        $rapportError = null;
        $rapports = [];
        $matricule = (string) ($_SESSION['matricule'] ?? '');

        if ($matricule === '') {
            $rapportError = 'Votre matricule de collaborateur est introuvable. Veuillez vous reconnecter.';
        } else {
            try {
                $rapports = getRapportsDuCollaborateur($matricule);
            } catch (PDOException $e) {
                error_log('Erreur lors du chargement des rapports du collaborateur : ' . $e->getMessage());
                $rapportError = 'Les rapports n’ont pas pu être chargés. Veuillez réessayer ultérieurement.';
            }
        }

        include("vues/v_mesRapports.php");
        break;
    }

    case 'rapportregion': {
        if ((int) ($_SESSION['habilitation'] ?? 0) !== 2) {
            include("vues/v_accesInterdit.php");
            break;
        }

        $rapportError = null;
        $rapports = [];
        $matricule = (string) ($_SESSION['matricule'] ?? '');

        if ($matricule === '') {
            $rapportError = 'Votre matricule de collaborateur est introuvable. Veuillez vous reconnecter.';
        } else {
            try {
                $rapports = getRapportsDeLaRegion($matricule);
            } catch (PDOException $e) {
                error_log('Erreur lors du chargement des rapports de la région : ' . $e->getMessage());
                $rapportError = 'Les rapports de la région n’ont pas pu être chargés. Veuillez réessayer ultérieurement.';
            }
        }

        include("vues/v_rapportRegion.php");
        break;
    }

    case 'voirrapport': {
        $numero = filter_var($_GET['numero'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        $proprietaire = isset($_GET['matricule']) && is_scalar($_GET['matricule'])
            ? trim((string) $_GET['matricule'])
            : '';
        $rapport = null;
        $echantillons = [];
        $rapportError = null;
        $isViewing = true;
        $isEditing = false;
        $editingReport = null;
        $formValues = [];

        if ($numero === false || $proprietaire === '') {
            $rapportError = 'Le rapport demandé est introuvable ou vous n’êtes pas autorisé à le consulter.';
        } else {
            try {
                $rapport = getRapportVisite(
                    $proprietaire,
                    $numero,
                    (string) ($_SESSION['matricule'] ?? ''),
                    (int) ($_SESSION['habilitation'] ?? 0)
                );
                if ($rapport) {
                    $echantillons = getEchantillonsRapport($proprietaire, $numero);
                } else {
                    $rapportError = 'Le rapport demandé est introuvable ou vous n’êtes pas autorisé à le consulter.';
                }
            } catch (PDOException $e) {
                error_log('Erreur lors du chargement du rapport : ' . $e->getMessage());
                $rapportError = 'Le rapport n’a pas pu être chargé. Veuillez réessayer ultérieurement.';
            }
        }

        $depuisRegion = (($_GET['source'] ?? '') === 'region')
            && (int) ($_SESSION['habilitation'] ?? 0) === 2;
        $rapportEditable = $rapport
            && $rapport['COL_MATRICULE'] === (string) ($_SESSION['matricule'] ?? '')
            && $rapport['RAP_ETAT'] === 'E';
        if ($rapport) {
            $editingReport = $rapport;
            $formValues = [
                'numeroRapport' => (string) $rapport['RAP_NUM'],
                'dateVisite' => (string) $rapport['RAP_DATEVISITE'],
                'matriculeCollaborateur' => (string) $rapport['COL_MATRICULE'],
                'motif' => (string) $rapport['id_motif'],
                'praticien' => (string) $rapport['PRA_NUM'],
                'remplacant' => (string) ($rapport['PRA_NUM_REMPLACANT'] ?? ''),
                'bilan' => (string) ($rapport['RAP_BILAN'] ?? ''),
                'saisieDefinitive' => $rapport['RAP_ETAT'] === 'V' ? '1' : '',
                'echantillons' => array_map(static function (array $echantillon): array {
                    return [
                        'medicament' => (string) $echantillon['medicament'],
                        'quantite' => (string) ($echantillon['quantite'] ?? ''),
                    ];
                }, $echantillons),
            ];
        }

        $praticiens = getAllNomPraticien();
        $motifs = getAllMotifsVisite();
        $medicaments = getAllMedicamentsPourRapport();
        $formTitle = 'Consulter un rapport de visite';
        $formSubmitLabel = '';
        $formAction = '#';
        include("vues/v_formRapport.php");
        break;
    }

    case 'formrapportdevisite':
    case 'modifierRapport': {
        $isEditing = $action === 'modifierRapport';
        $editingReport = null;
        $numeroEdition = null;

        if ($isEditing) {
            $numeroEdition = filter_var($_GET['numero'] ?? null, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1]
            ]);
            $matricule = (string) ($_SESSION['matricule'] ?? '');
            if ($numeroEdition === false || $matricule === '') {
                $_SESSION['alert'] = ['type' => 'danger', 'text' => 'Le rapport demandé est introuvable.'];
                header('Location: index.php?uc=rapport&action=mesrapports');
                exit;
            }

            try {
                $editingReport = getRapportVisite($matricule, $numeroEdition, $matricule, (int) ($_SESSION['habilitation'] ?? 0));
                if (!$editingReport) {
                    $_SESSION['alert'] = ['type' => 'danger', 'text' => 'Ce rapport est introuvable ou ne vous appartient pas.'];
                    header('Location: index.php?uc=rapport&action=mesrapports');
                    exit;
                }
                if ($editingReport['RAP_ETAT'] !== 'E') {
                    $_SESSION['alert'] = ['type' => 'warning', 'text' => 'Ce rapport est définitif et ne peut plus être modifié.'];
                    header('Location: index.php?uc=rapport&action=voirrapport&matricule=' . rawurlencode($matricule) . '&numero=' . $numeroEdition);
                    exit;
                }
                $echantillonsExistants = getEchantillonsRapport($matricule, $numeroEdition);
            } catch (PDOException $e) {
                error_log('Erreur lors du chargement du rapport à modifier : ' . $e->getMessage());
                $_SESSION['alert'] = ['type' => 'danger', 'text' => 'Le rapport n’a pas pu être chargé.'];
                header('Location: index.php?uc=rapport&action=mesrapports');
                exit;
            }
        }

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
        if ($isEditing) {
            $formValues = array_merge([
                'numeroRapport' => (string) $editingReport['RAP_NUM'],
                'dateVisite' => (string) $editingReport['RAP_DATEVISITE'],
                'motif' => (string) $editingReport['id_motif'],
                'praticien' => (string) $editingReport['PRA_NUM'],
                'remplacant' => (string) ($editingReport['PRA_NUM_REMPLACANT'] ?? ''),
                'bilan' => (string) ($editingReport['RAP_BILAN'] ?? ''),
                'echantillons' => array_map(static function (array $echantillon): array {
                    return [
                        'medicament' => (string) $echantillon['medicament'],
                        'quantite' => (string) ($echantillon['quantite'] ?? ''),
                    ];
                }, $echantillonsExistants),
            ], $formValues);
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
                if ($isEditing && $numero !== $numeroEdition) {
                    throw new InvalidArgumentException('Le numéro du rapport ne peut pas être modifié.');
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

                $donneesRapport = [
                    'matricule' => $matricule,
                    'numero' => $numero,
                    'date_visite' => $dateVisite,
                    'bilan' => $bilan,
                    'definitif' => $postString('saisieDefinitive') === '1',
                    'praticien' => $praticien,
                    'motif' => $motif,
                    'remplacant' => $remplacant,
                ];
                if ($isEditing) {
                    modifierRapportVisite($matricule, $numeroEdition, $donneesRapport, $echantillons);
                } else {
                    enregistrerRapportVisite($donneesRapport, $echantillons);
                }

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'text' => $isEditing
                        ? 'Le rapport de visite a été modifié avec succès.'
                        : 'Le rapport de visite a été enregistré avec succès.'
                ];
                header($isEditing
                    ? 'Location: index.php?uc=rapport&action=voirrapport&matricule=' . rawurlencode($matricule) . '&numero=' . $numeroEdition
                    : 'Location: index.php?uc=rapport&action=formrapportdevisite');
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

        $formAction = $isEditing
            ? 'index.php?uc=rapport&action=modifierRapport&numero=' . $numeroEdition
            : 'index.php?uc=rapport&action=formrapportdevisite';
        $formTitle = $isEditing ? 'Modifier un rapport de visite' : 'Rédiger un rapport de visite';
        $formSubmitLabel = $isEditing ? 'Enregistrer les modifications' : 'Enregistrer le rapport';
        include("vues/v_formRapport.php");
        break;
    }

    default: {
        header("Location: index.php?uc=rapport&action=mesrapports");
        break;
    }
}
?>