<!-- Liste déroulante qui permet de choisir un praticien uc=gestionPraticiens&action=formulaireGestion -->

<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Formulaire de gestion des praticiens</h1>

            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= $_SESSION['alert']['type'] ?> text-center">
                    <?= $_SESSION['alert']['text'] ?>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <p class="text text-center">
                Formulaire permettant de modifier des informations à propos d'un praticien en particulier : à faire
            </p>

        </div>
        
    </div>
</section>