<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">
                Informations du Médicament 
                <span class="carac">
                    <?= htmlspecialchars("(".$info['MED_DEPOTLEGAL']) . ') - ' . htmlspecialchars($info['MED_NOMCOMMERCIAL']) ?>
                </span>
            </h1>
        </div>

        <div class="row align-items-center justify-content-center">
            <!-- Image -->
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/medoc.jpeg" alt="Photo du praticien">
            </div>

            <!-- Informations du praticien -->
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <div class="formulaire">
                    <p><span class="carac">Nom du dépôt légal :</span> <?= htmlspecialchars($info['MED_DEPOTLEGAL']) ?></p>
                    <p><span class="carac">Nom commercial :</span> <?= htmlspecialchars($info['MED_NOMCOMMERCIAL']) ?></p>
                    <p><span class="carac">Composition :</span> <?= htmlspecialchars($info['MED_COMPOSITION']) ?></p>
                    <p><span class="carac">Effets :</span> <?= htmlspecialchars($info['MED_EFFETS']) ?></p>
                    <p><span class="carac">Contre-indication :</span> <?= htmlspecialchars($info['MED_CONTREINDIC']) ?></p>
                    <p><span class="carac">Prix de l'échantillon :</span> <?= htmlspecialchars($info['MED_PRIXECHANTILLON']) ?></p>
                    <p><span class="carac">Famille :</span> <?= htmlspecialchars("(".$info['FAM_CODE'].") ".$info['FAM_LIBELLE']) ?></p>

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