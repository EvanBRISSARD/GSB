<head>
    <title>Projet GSB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/boxicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gsb.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <nav id="main_nav" class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="menuCont container">
            <a class="navbar-brand h1 my-2" href="index.php?uc=accueil">
                <span class="text-dark h4 fw-bold">Projet</span>
                <span class="text-info h4 fw-bold">GSB</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar-toggler-success" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill d-lg-flex justify-content-lg-between"
                id="navbar-toggler-success">

                <div class="flex-fill d-flex justify-content-end">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-xl-5 text-center text-dark">

                        <li class="nav-item">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=accueil">Accueil</a>
                        </li>



                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-outline-info rounded-pill px-3 fw-bold" href="#"
                                id="dropdownPraticiens" role="button" data-bs-toggle="dropdown">
                                Praticiens
                            </a>

                            <ul class="dropdown-menu text-center">

                                <!-- Visible pour tout utilisateur connecté (y compris visiteur) -->
                                <li>
                                    <a class="dropdown-item fw-bold"
                                        href="index.php?uc=praticiens&action=formulairepraticien">
                                        Afficher tous les praticiens
                                    </a>
                                </li>

                                <!-- Visible uniquement pour habilitation 2 ou 3 (délégué ou responsable) -->
                                <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3): ?>

                                    <li>
                                        <a class="dropdown-item fw-bold"
                                            href="index.php?uc=gestionPraticiens&action=formulaireGestion">
                                            Gérer les praticiens
                                        </a>
                                    </li>

                                <?php endif; ?>

                            </ul>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=profil">Profil</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=deconnexion"
                                onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">Déconnexion</a>
                        </li>

                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <?php if (!empty($_SESSION['alert'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show text-center" role="alert">
                <?= $_SESSION['alert']['text'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>