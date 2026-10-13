<!-- Formulaire de sélection d'un praticien -->

<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">

            <h1 class="titre text-center">
                Formulaire de gestion des praticiens
            </h1>

            <?php if (!empty($_SESSION['alert'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['alert']['type']) ?> text-center">
                    <?= htmlspecialchars($_SESSION['alert']['text']) ?>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <p class="text text-center">
                Sélectionnez un praticien pour accéder à ses informations.
            </p>

            <form method="POST"
                  action="index.php?uc=gestionPraticiens&action=modifierPraticien"
                  class="mt-4">

                <div class="mb-3">
                    <label for="praticien" class="form-label">
                        Choisir un praticien :
                    </label>

                    <select name="praticien"
                            id="praticien"
                            class="form-select"
                            required>

                        <option value="">-- Sélectionner un praticien --</option>

                        <?php foreach ($result as $unPraticien): ?>
                            <option value="<?= htmlspecialchars($unPraticien['PRA_NUM']) ?>">
                                <?= htmlspecialchars(
                                    $unPraticien['PRA_NOM'] . ' ' .
                                    $unPraticien['PRA_PRENOM']
                                ) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        Modifier ce praticien
                    </button>
                </div>

            </form>

        </div>
    </div>
</section>