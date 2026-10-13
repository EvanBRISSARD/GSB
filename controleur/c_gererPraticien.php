<?php

// Accès réservé aux délégués ou responsables de secteur
if (empty($_SESSION['login'])) {
    include("vues/v_accesInterdit.php");
    exit;
}

// Modèle praticien
include_once __DIR__ . '/../modele/praticien.modele.inc.php';

// L'action par défaut est formulaireGestion
$action = $_REQUEST['action'] ?? 'formulaireGestion';

switch ($action) {

    case 'formulairepraticien': {

        // $_SESSION['login'] contient probablement directement le matricule
        $matricule = $_SESSION['login'];

        // Récupère la liste complète des praticiens (fonction existante dans le modèle)
        $result = getAllNomPraticien();

        include("vues/v_formulairePraticien.php");
        break;
    }

    // Affichage des informations d'un praticien sélectionné
    case 'afficherpraticien': {

        if (!empty($_REQUEST['praticien'])) {

            $pra = $_REQUEST['praticien'];

            // Récupère toutes les infos du praticien
            $info = getAllInformationPraticien($pra);

            include("vues/v_afficherPraticien.php");

        } else {

            // On renvoie vers le formulaire si aucun praticien n’a été choisi
            $_SESSION['erreur'] = true;
            header("Location: index.php?uc=praticiens&action=formulairepraticien");
            exit;
        }
        break;
    }


    case 'formulaireGestion': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        // Délégué : praticiens de sa région
        // Responsable de secteur : praticiens de tout son secteur
        $result = getPraticiensAccessiblesGestion($_SESSION['matricule'], $_SESSION['habilitation']);

        // Affichage du formulaire de gestion
        include("vues/v_formulaireGererPraticien.php");
        break;
    }

    case 'modifierPraticien': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        // Récupération du numéro du praticien (POST ou GET)
        if (!empty($_POST['praticien'])) {
            $num = $_POST['praticien'];
        } elseif (!empty($_GET['num'])) {
            $num = $_GET['num'];
        } else {
            // Aucun praticien sélectionné
            $_SESSION['alert'] = ["type" => "warning", "text" => "Veuillez sélectionner un praticien."];
            header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
            exit;
        }

        // Récupération des données nécessaires au formulaire
        $lesTypes = getAllTypesPraticien();
        $lesSpecialites = getAllSpecialites();

        // Si on revient d'une erreur ou d'une redirection, on recharge les données saisies depuis la session
        if (
            !empty($_SESSION['tempForm']) && !empty($_SESSION['tempForm']['num'])
            && (string) $_SESSION['tempForm']['num'] === (string) $num
        ) {

            $val = $_SESSION['tempForm'];

            // Pré-remplissage du praticien avec les valeurs saisies
            $praticien = [
                'PRA_NUM' => $val['num'],
                'PRA_NOM' => $val['nom'],
                'PRA_PRENOM' => $val['prenom'],
                'PRA_ADRESSE' => $val['adresse'],
                'PRA_CP' => $val['cp'],
                'PRA_VILLE' => $val['ville'],
                'PRA_COEFNOTORIETE' => $val['coef'],
                'PRA_COEFCONFIANCE' => $val['coefConfiance'] ?? '',
                'TYP_CODE' => $val['type'] ?? null
            ];

            // Spécialités sélectionnées précédemment
            $specialitesPraticien = $val['specialites'] ?? [];

            // Nettoyage de la session temporaire
            unset($_SESSION['tempForm']);

        } else {

            // Récupérer les données depuis la base
            $praticien = getAllInformationPraticien($num);

            // Si le praticien n'existe pas
            if (!$praticien) {
                $_SESSION['alert'] = ["type" => "danger", "text" => "Le praticien demandé n’existe pas."];
                header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
                exit;
            }

            // Vérifie que le praticien appartient bien à la région du délégué ou au secteur complet du responsable de secteur
            if (!praticienAccessibleGestion($num, $_SESSION['matricule'], $_SESSION['habilitation'])) {
                $_SESSION['alert'] = ["type" => "danger", "text" => "Vous ne pouvez pas modifier ce praticien."];
                header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
                exit;
            }

            // Récupérer les spécialités du praticien
            $specialitesPraticien = getSpecialitesPraticien($num);
        }

        // Affichage de la vue de modification
        include("vues/v_modifierPraticien.php");
        break;
    }

    case 'validerPraticien': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        // Récupération et nettoyage des champs du formulaire
        $num = trim($_POST['num']);
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $adresse = trim($_POST['adresse']);
        $cp = trim($_POST['cp']);
        $ville = trim($_POST['ville']);
        $coef = str_replace(',', '.', trim($_POST['coef']));
        $coefConfiance = str_replace(',', '.', trim($_POST['coefConfiance']));
        $type = isset($_POST['type']) ? trim($_POST['type']) : null;
        $spe = $_POST['specialites'] ?? [];

        // Le type peut être NULL
        if ($type === "") {
            $type = null;
        }

        // Impossible de modifier un praticien inexistant
        if (!praticienExiste($num)) {
            $_SESSION['alert'] = ["type" => "danger", "text" => "Impossible de modifier : praticien inexistant."];
            header("Location: index.php?uc=gestionPraticiens&action=creerPraticien");
            exit;
        }

        if (!praticienAccessibleGestion($num, $_SESSION['matricule'], $_SESSION['habilitation'])) {
            $_SESSION['alert'] = ["type" => "danger", "text" => "Vous ne pouvez pas modifier ce praticien."];
            header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
            exit;
        }

        // Récupération de l'ancien cp
        $ancienCp = getAncienCpPraticien($num);

        // Si le cp change, vérification de la région
        if ($ancienCp !== null && $ancienCp !== $cp) {

            $pdo = connexionPDO();

            // Départements ancien et nouveau
            // Utilisation de substr : substr(chaine, position_depart, longueur);
            $depAncien = substr($ancienCp, 0, 2);
            $depNouveau = substr($cp, 0, 2);

            // Requête pour récupérer la région d'un département
            $sql = "SELECT r.REG_CODE
                    FROM departement d
                    JOIN region r ON r.REG_CODE = d.REG_CODE
                    WHERE d.NoDEPT = :dep";

            $stmt = $pdo->prepare($sql);

            // Région ancienne
            $stmt->execute([":dep" => $depAncien]);
            $regionAncienne = $stmt->fetchColumn();

            // Région nouvelle
            $stmt->execute([":dep" => $depNouveau]);
            $regionNouvelle = $stmt->fetchColumn();

            // Interdiction de changer de région
            if ($regionNouvelle !== $regionAncienne) {

                $_SESSION['alert'] = [
                    "type" => "danger",
                    "text" => "Impossible de modifier le code postal : le praticien sortirait de sa région."
                ];

                // Sauvegarde des données pour rechargement du formulaire pré-rempli
                $_SESSION['tempForm'] = [
                    "num" => $num,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "adresse" => $adresse,
                    "cp" => $cp,
                    "ville" => $ville,
                    "coef" => $coef,
                    "coefConfiance" => $coefConfiance,
                    "type" => $type,
                    "specialites" => $spe
                ];

                header("Location: index.php?uc=gestionPraticiens&action=modifierPraticien&num=$num");
                exit;
            }
        }

        // Mise à jour des informations et des spécialités
        $modifPraticien = updatePraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $coefConfiance, $type);
        $modifSpecialites = updateSpecialitesPraticien($num, $spe);

        // Message de succès ou d'erreur
        if ($modifPraticien || $modifSpecialites) {
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Modifications enregistrées avec succès."
            ];
        } else {
            $_SESSION['alert'] = [
                "type" => "warning",
                "text" => "Aucune modification n'a été faite."
            ];
        }

        header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
        exit;
    }

    case 'creerPraticien': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        // Numéro automatique du prochain praticien (si le dernier praticien enregistré en bdd a le numéro 86, le suivant aura le numéro 87)
        $nextNum = getPraticienNum();

        // Données nécessaires au formulaire
        $types = getAllTypesPraticien();
        $lesSpecialites = getAllSpecialites();

        include("vues/v_creerPraticien.php");
        break;
    }

    case 'validerCreerPraticien': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        // Récupération des champs du formulaire
        $num = trim($_POST['num']);
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $adresse = trim($_POST['adresse']);
        $cp = trim($_POST['cp']);
        $ville = trim($_POST['ville']);
        $coef = str_replace(',', '.', trim($_POST['coef']));
        $coefConfiance = str_replace(',', '.', trim($_POST['coefConfiance']));
        $type = isset($_POST['type']) ? trim($_POST['type']) : null;
        $spe = $_POST['specialites'] ?? [];

        if ($type === "") {
            $type = null;
        }

        // Validation des champs dans le formulaire
        $erreurs = [];

        // Utilisation de preg_match : preg_match("regex", $chaine);
        if (!preg_match("/^[a-zA-ZÀ-ÿ\- ]+$/u", $nom))
            $erreurs[] = "Nom invalide.";
        if (!preg_match("/^[a-zA-ZÀ-ÿ\- ]+$/u", $prenom))
            $erreurs[] = "Prénom invalide.";
        if (!preg_match("/^[0-9]{5}$/", $cp))
            $erreurs[] = "Code postal invalide.";
        if (!preg_match("/^[a-zA-ZÀ-ÿ\- ]+$/u", $ville))
            $erreurs[] = "Ville invalide.";
        if (!preg_match("/^[0-9]{1,9}(\.[0-9]{1,2})?$/", $coef))
            $erreurs[] = "Coefficient de notoriété invalide.";
        if (!preg_match("/^[0-9]{1,9}(\.[0-9]{1,2})?$/", $coefConfiance))
            $erreurs[] = "Coefficient de confiance invalide.";

        // En cas d'erreurs, retour au formulaire
        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            $_SESSION['old'] = $_POST;
            header("Location: index.php?uc=gestionPraticiens&action=creerPraticien");
            exit;
        }

        // Si aucune spécialité ou type, demander confirmation
        $aucunType = ($type === null);
        $aucuneSpe = empty($spe);

        // Confirmation par le visiteur
        $confirmation = isset($_POST['confirmer']) && $_POST['confirmer'] == 1;

        // Redirection vers la confirmation si besoin
        if (($aucunType || $aucuneSpe) && !$confirmation) {

            $_SESSION['tempForm'] = $_POST;
            $_SESSION['tempForm']['type'] = $type;
            $_SESSION['tempForm']['specialites'] = $spe;

            header("Location: index.php?uc=gestionPraticiens&action=confirmerCreationPraticien");
            exit;
        }

        // Création en bdd
        insererPraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $coefConfiance, $type);
        insererSpecialitesPraticien($num, $spe);

        $_SESSION['alert'] = [
            "type" => "success",
            "text" => "Praticien créé avec succès."
        ];

        // Redirection vers la modification
        header("Location: index.php?uc=gestionPraticiens&action=modifierPraticien&num=$num");
        exit;
    }

    case 'confirmerCreationPraticien': {

        if (($_SESSION['habilitation'] == 1)) {
            include("vues/v_accesInterdit.php");
            exit;
        }

        if (empty($_SESSION['tempForm'])) {
            header("Location: index.php?uc=gestionPraticiens&action=creerPraticien");
            exit;
        }

        include("vues/v_confirmerCreerPraticien.php");
        break;
    }

    default: {
        header("Location: index.php?uc=gestionPraticiens&action=formulaireGestion");
        exit;
    }
}