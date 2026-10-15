<?php

ob_start();


require_once('modele/collaborateur.modele.inc.php');

require_once('modele/connexion.modele.inc.php');



if (!isset($_REQUEST['uc']) || empty($_REQUEST['uc'])) {
    $uc = 'accueil';
} else {
    $uc = $_REQUEST['uc'];
}

if (empty($_SESSION['login'])) {
    include("vues/v_headerDeconnexion.php");
} else {
    include("vues/v_header.php");
}


switch ($uc) {
    case 'accueil': {
        include("vues/v_accueil.php");
        break;
    }

    case 'praticiens': {  // Si uc=praticiens

        if (!empty($_SESSION['login'])) {  // Si l'utilisateur est connecté
            include("controleur/c_gererPraticien.php");
        } else {  // Si l'utilisateur n'est pas connecté
            include("vues/v_accesInterdit.php");
        }

        break;
    }

    case 'connexion': {

        include("controleur/c_connexion.php");
        break;
    }


    case 'gestionPraticiens': {

        if (!empty($_SESSION['login']) && ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3)) {  // Seuls les délégués et les responsables de secteur ont accès
            include("controleur/c_gererPraticien.php");
        } else {  // Sinon accès interdit
            include("vues/v_accesInterdit.php");
        }

        break;
    }

    case 'medicaments' : {
        
        if (!empty($_SESSION['login'])) {  // Si l'utilisateur est connecté
            include("controleur/c_gererMedicament");
        } else {  // Si l'utilisateur n'est pas connecté
            include("vues/v_accesInterdit.php");
        }

        break;
    }

    default: {
        include("vues/v_accueil.php");
        break;
    }
}

include("vues/v_footer.php");

ob_end_flush();
?>