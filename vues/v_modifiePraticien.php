<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">

            <h1 class="titre text-center">
                Modifier un praticien
            </h1>

            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['alert']['type']) ?> text-center">
                    <?= htmlspecialchars($_SESSION['alert']['text']) ?>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <form method="POST"
                  action="index.php?uc=gestionPraticiens&action=validerPraticien">

                <input type="hidden" name="num"
                       value="<?= htmlspecialchars($praticien['PRA_NUM']) ?>">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" class="form-control" id="nom"
                           name="nom" required
                           value="<?= htmlspecialchars($praticien['PRA_NOM']) ?>">
                </div>

                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" class="form-control" id="prenom"
                           name="prenom" required
                           value="<?= htmlspecialchars($praticien['PRA_PRENOM']) ?>">
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse :</label>
                    <input type="text" class="form-control" id="adresse"
                           name="adresse" required
                           value="<?= htmlspecialchars($praticien['PRA_ADRESSE']) ?>">
                </div>

                <div class="mb-3">
                    <label for="cp" class="form-label">Code postal :</label>
                    <input type="text" class="form-control" id="cp"
                           name="cp" required
                           value="<?= htmlspecialchars($praticien['PRA_CP']) ?>">
                </div>

                <div class="mb-3">
                    <label for="ville" class="form-label">Ville :</label>
                    <input type="text" class="form-control" id="ville"
                           name="ville" required
                           value="<?= htmlspecialchars($praticien['PRA_VILLE']) ?>">
                </div>

                <div class="mb-3">
                    <label for="coef" class="form-label">
                        Coefficient de notoriété :
                    </label>
                    <input type="number" step="0.01" min="0"
                           class="form-control" id="coef"
                           name="coef" required
                           value="<?= htmlspecialchars($praticien['PRA_COEFNOTORIETE']) ?>">
                </div>

                <div class="mb-3">
                    <label for="coefConfiance" class="form-label">
                        Coefficient de confiance :
                    </label>
                    <input type="number" step="0.01" min="0"
                           class="form-control" id="coefConfiance"
                           name="coefConfiance" required
                           value="<?= htmlspecialchars($praticien['PRA_COEFCONFIANCE']) ?>">
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">
                        Type de praticien :
                    </label>

                    <select class="form-select" id="type" name="type">
                        <option value="">-- Aucun type --</option>

                        <?php foreach ($lesTypes as $unType): ?>
                            <option
                                value="<?= htmlspecialchars($unType['TYP_CODE']) ?>"
                                <?= ($praticien['TYP_CODE'] == $unType['TYP_CODE']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($unType['TYP_LIBELLE']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        Enregistrer les modifications
                    </button>

                    <a href="index.php?uc=gestionPraticiens&action=formulaireGestion"
                       class="btn btn-secondary">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>