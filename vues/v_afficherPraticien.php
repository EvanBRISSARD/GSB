<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">
                Informations du praticien 
                <span class="carac">
                    <?= htmlspecialchars($info['PRA_NOM']) . ' ' . htmlspecialchars($info['PRA_PRENOM']) ?>
                </span>
            </h1>
        </div>

        <div class="row align-items-center justify-content-center">
            <!-- Image -->
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/praticien.jpg" alt="Photo du praticien">
            </div>

            <!-- Informations du praticien -->
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <div class="formulaire">
                    <p><span class="carac">Numéro :</span> <?= htmlspecialchars($info['PRA_NUM']) ?></p>
                    <p><span class="carac">Nom :</span> <?= htmlspecialchars($info['PRA_NOM']) ?></p>
                    <p><span class="carac">Prénom :</span> <?= htmlspecialchars($info['PRA_PRENOM']) ?></p>
                    <p><span class="carac">Adresse :</span> <?= htmlspecialchars($info['PRA_ADRESSE']) ?></p>
                    <p><span class="carac">Code postal :</span> <?= htmlspecialchars($info['PRA_CP']) ?></p>
                    <p><span class="carac">Ville :</span> <?= htmlspecialchars($info['PRA_VILLE']) ?></p>
                    <p><span class="carac">Coefficient de notoriété :</span> <?= htmlspecialchars($info['PRA_COEFNOTORIETE']) ?></p>
                    <p><span class="carac">Coefficient de confiance :</span> <?= htmlspecialchars($info['PRA_COEFCONFIANCE']) ?></p>
                    <p><span class="carac">Type de praticien :</span> <?= htmlspecialchars($info['TYP_LIBELLE']) ?></p>

                    <input 
                        class="btn btn-info text-light valider col-6 col-sm-5 col-md-4 col-lg-3 mt-3" 
                        type="button" 
                        onclick="history.go(-1)" 
                        value="Retour">
                </div>
            </div>
        </div>
    </div>
</section>