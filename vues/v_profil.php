

<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Profil</h1>
            <p class="text text-center">
                <?php echo (isset($infoNonPersonnelle))? "informations du collaborateur : " .htmlspecialchars($info['matricule']) . " " . $info['prenom'] : "informations personnelles."; ?>
            </p>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid w-75" src="assets/img/profil.png">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <div class="formulaire">

                    <p><span class="carac">Matricule</span> : <?php echo htmlspecialchars($info['matricule']) ?></p>
                    <p><span class="carac">Nom</span> : <?php echo htmlspecialchars($info['nom']) ?></p>
                    <p><span class="carac">Prenom</span> : <?php echo htmlspecialchars($info['prenom']) ?></p>
                    <p><span class="carac">Rue</span> : <?php echo htmlspecialchars($info['adresse']) ?></p>
                    <p><span class="carac">Code Postal</span> : <?php echo htmlspecialchars($info['cp']) ?></p>
                    <p><span class="carac">Ville</span> : <?php echo htmlspecialchars($info['ville']) ?></p>
                    <p><span class="carac">Habilitation</span> : <span style="color:#0DCAF0;font-weight: 700;"> <?php echo htmlspecialchars($info['habilitation']) ?></span></p>
                    <p><span class="carac">Secteur</span> : <?php echo htmlspecialchars($info['secteur']) ?></p>
                    <p><span class="carac">Région</span> : <?php echo htmlspecialchars($info['region']) ?></p>

                <?php if (!isset($infoNonPersonnelle) || (isset($droitDeModificationMajeur) && $droitDeModificationMajeur)) { ?>
                    <a href="index.php?uc=collaborateur&action=modifier&matricule=<?php echo htmlspecialchars($info['matricule']);?>">
                    <button class="btn btn-info text-light valider" type="submit" >retour accueil</button>
                    </a>
                <?php } ?>
                </div>
            </div>
        </div>
</section>