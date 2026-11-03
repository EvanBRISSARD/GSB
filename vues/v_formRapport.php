<section class="bg-light">
    <div class="container pb-5">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center"><?= htmlspecialchars($formTitle ?? 'Rédiger un rapport de visite', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text text-center">
                <?= !empty($isViewing) ? 'Consultez les informations de la visite et son bilan.' : 'Renseignez les informations de la visite et son bilan.' ?>
            </p>
        </div>

        <?php if (!empty($rapportError)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($rapportError, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <a class="btn btn-outline-secondary" href="<?= !empty($depuisRegion) ? 'index.php?uc=rapport&amp;action=rapportregion' : 'index.php?uc=rapport&amp;action=mesrapports' ?>">
                Retour à la liste
            </a>
        <?php else: ?>
        <?php if (!empty($isViewing) && !empty($_SESSION['alert'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['alert']['type'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($_SESSION['alert']['text'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
        <?php if (!empty($isViewing) && ($editingReport['RAP_ETAT'] ?? '') === 'V'): ?>
            <div class="alert alert-info" role="status">Ce rapport est définitif. Sa modification est verrouillée.</div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12">
                <form class="rapport-formulaire" action="<?= htmlspecialchars($formAction ?? 'index.php?uc=rapport&amp;action=formrapportdevisite', ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <?php if (empty($isViewing)): ?>
                        <p class="text-muted mb-3"><span class="text-danger">*</span> Champs obligatoires</p>
                    <?php endif; ?>
                    <?php if (!empty($formError)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4 align-items-stretch">
                        <div class="col-12 col-lg-7">
                            <section class="bg-white border rounded-3 shadow-sm p-3 p-md-4 h-100">
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="assets/img/rapport.png" alt="Logo de rapport" class="img-fluid rounded-3 shadow-sm p-2 bg-light" style="width: 60px; height: 60px; object-fit: contain;">
                                        <div>
                                            <h2 class="h4 mb-1">Informations du rapport</h2>
                                            <p class="text-muted mb-0">Les informations principales de votre visite.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="numeroRapport">Numéro de rapport <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" id="numeroRapport" name="numeroRapport" min="1" required
                                            <?= !empty($isEditing) || !empty($isViewing) ? 'readonly' : '' ?>
                                            value="<?= htmlspecialchars((string) ($formValues['numeroRapport'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="dateVisite">Date de visite <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" id="dateVisite" name="dateVisite" required
                                            <?= !empty($isViewing) ? 'readonly' : '' ?>
                                            value="<?= htmlspecialchars((string) ($formValues['dateVisite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="matriculeCollaborateur">Matricule du collaborateur</label>
                                        <input class="form-control" type="text" id="matriculeCollaborateur" name="matriculeCollaborateur"
                                            value="<?= htmlspecialchars((string) ($formValues['matriculeCollaborateur'] ?? $_SESSION['matricule'] ?? $_SESSION['login'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                            readonly>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="dateSaisie">Date de saisie</label>
                                        <input class="form-control" type="date" id="dateSaisie" name="dateSaisie"
                                            value="<?= htmlspecialchars((string) ($editingReport['RAP_DATESAISIE'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>" readonly>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="motif">Motif <span class="text-danger">*</span></label>
                                        <select class="form-select" id="motif" name="motif" required <?= !empty($isViewing) ? 'disabled' : '' ?>>
                                            <option value="" disabled <?= empty($formValues['motif']) ? 'selected' : '' ?>>Choisissez un motif</option>
                                            <?php foreach ($motifs as $motif): ?>
                                                <option value="<?= htmlspecialchars((string) $motif['id_motif'], ENT_QUOTES, 'UTF-8') ?>"
                                                    <?= (string) ($formValues['motif'] ?? '') === (string) $motif['id_motif'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($motif['libelle_motif'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="praticien">Praticien concerné <span class="text-danger">*</span></label>
                                        <select class="form-select" id="praticien" name="praticien" required <?= !empty($isViewing) ? 'disabled' : '' ?>>
                                            <option value="" disabled <?= empty($formValues['praticien']) ? 'selected' : '' ?>>Choisissez un praticien</option>
                                            <?php foreach ($praticiens as $praticien): ?>
                                                <option value="<?= htmlspecialchars((string) $praticien['PRA_NUM'], ENT_QUOTES, 'UTF-8') ?>"
                                                    <?= (string) ($formValues['praticien'] ?? '') === (string) $praticien['PRA_NUM'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($praticien['PRA_NUM'] . ' - ' . $praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="remplacant">Remplaçant</label>
                                        <select class="form-select" id="remplacant" name="remplacant" <?= !empty($isViewing) ? 'disabled' : '' ?>>
                                            <option value="" <?= empty($formValues['remplacant']) ? 'selected' : '' ?>>Aucun remplaçant</option>
                                            <?php foreach ($praticiens as $praticien): ?>
                                                <option value="<?= htmlspecialchars((string) $praticien['PRA_NUM'], ENT_QUOTES, 'UTF-8') ?>"
                                                    <?= (string) ($formValues['remplacant'] ?? '') === (string) $praticien['PRA_NUM'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($praticien['PRA_NUM'] . ' - ' . $praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM'], ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="bilan">Bilan du rapport <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="bilan" name="bilan" rows="6" maxlength="255" required <?= !empty($isViewing) ? 'readonly' : '' ?>><?= htmlspecialchars((string) ($formValues['bilan'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-12 col-lg-5">
                            <section class="echantillons-panel bg-white border rounded-3 shadow-sm p-3 p-md-4 h-100">
                                <div class="echantillons-entete mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="assets/img/pillule.png" alt="Logo de rapport" class="img-fluid rounded-3 shadow-sm p-2 bg-light" style="width: 60px; height: 60px; object-fit: contain;">
                                        <div>
                                            <h2 class="h4 mb-1">Échantillons remis</h2>
                                            <p class="text-muted mb-0">Associez les produits et les quantités remis au praticien.</p>
                                        </div>
                                    </div>
                                    <span class="echantillons-compteur" id="echantillonsCompteur" aria-live="polite">0 produit</span>
                                </div>

                                <div id="echantillonsListe">
                                    <div class="row g-3 echantillon-ligne align-items-end mb-3">
                                        <div class="col-7">
                                            <label class="form-label" for="echantillonMedicament0">Médicament</label>
                                            <select class="form-select echantillon-medicament" id="echantillonMedicament0" name="echantillons[0][medicament]" <?= !empty($isViewing) ? 'disabled' : '' ?>>
                                                <option value="">Choisissez un médicament</option>
                                                <?php foreach ($medicaments as $medicament): ?>
                                                    <option value="<?= htmlspecialchars($medicament['MED_DEPOTLEGAL'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($medicament['MED_NOMCOMMERCIAL'] . ' (' . $medicament['MED_DEPOTLEGAL'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <label class="form-label" for="echantillonQuantite0">Quantité</label>
                                            <input class="form-control echantillon-quantite" type="number" id="echantillonQuantite0"
                                                name="echantillons[0][quantite]" min="1" step="1" <?= !empty($isViewing) ? 'readonly' : '' ?>>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-outline-secondary w-100 retirer-echantillon" type="button" aria-label="Retirer cet échantillon" title="Retirer cet échantillon" hidden>
                                                <i class="bi bi-trash3" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="echantillons-pied mt-4">
                                    <button class="btn btn-info text-white" type="button" id="ajouterEchantillon" <?= !empty($isViewing) ? 'hidden' : '' ?>>
                                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Ajouter un échantillon
                                    </button>
                                    <p class="small text-muted mb-0 mt-2">Chaque produit doit avoir une quantité entière supérieure à zéro.</p>
                                </div>
                            </section>
                        </div>

                        <template id="modeleEchantillon">
                            <div class="row g-3 echantillon-ligne align-items-end mb-3">
                                <div class="col-7">
                                    <label class="form-label" for="echantillonMedicament__INDEX__">Médicament</label>
                                    <select class="form-select echantillon-medicament" id="echantillonMedicament__INDEX__" name="echantillons[__INDEX__][medicament]">
                                        <option value="">Choisissez un médicament</option>
                                        <?php foreach ($medicaments as $medicament): ?>
                                            <option value="<?= htmlspecialchars($medicament['MED_DEPOTLEGAL'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($medicament['MED_NOMCOMMERCIAL'] . ' (' . $medicament['MED_DEPOTLEGAL'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="echantillonQuantite__INDEX__">Quantité</label>
                                    <input class="form-control echantillon-quantite" type="number" id="echantillonQuantite__INDEX__"
                                        name="echantillons[__INDEX__][quantite]" min="1" step="1">
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-outline-secondary w-100 retirer-echantillon" type="button" aria-label="Retirer cet échantillon" title="Retirer cet échantillon">
                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="col-12">
                            <div class="bg-white border rounded-3 shadow-sm p-3 p-md-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div class="form-check">
                                    <?php if (!empty($isViewing)): ?>
                                        <h4>
                                            <span class="badge <?= ($editingReport['RAP_ETAT'] ?? '') === 'V' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= ($editingReport['RAP_ETAT'] ?? '') === 'V' ? 'Définitif' : 'En cours' ?>
                                            </span>
                                        </h4>
                                    <?php else: ?>
                                        <input class="form-check-input" type="checkbox" id="saisieDefinitive" name="saisieDefinitive" value="1"
                                            <?= isset($formValues['saisieDefinitive']) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="saisieDefinitive">Saisie définitive</label>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if (!empty($isViewing) && !empty($rapportEditable)): ?>
                                        <a class="btn btn-info text-light px-4"
                                            href="index.php?uc=rapport&amp;action=modifierRapport&amp;numero=<?= (int) $editingReport['RAP_NUM'] ?>">
                                            Modifier ce rapport
                                        </a>
                                    <?php elseif (empty($isViewing)): ?>
                                        <button class="btn btn-info text-light px-4" type="submit"><?= htmlspecialchars($formSubmitLabel ?? 'Enregistrer le rapport', ENT_QUOTES, 'UTF-8') ?></button>
                                    <?php endif; ?>
                                    <a class="btn btn-outline-secondary px-4"
                                        href="<?php if (!empty($isViewing)): ?><?= !empty($depuisRegion) ? 'index.php?uc=rapport&amp;action=rapportregion' : 'index.php?uc=rapport&amp;action=mesrapports' ?><?php elseif (!empty($isEditing)): ?>index.php?uc=rapport&amp;action=voirrapport&amp;matricule=<?= rawurlencode((string) ($_SESSION['matricule'] ?? '')) ?>&amp;numero=<?= (int) ($editingReport['RAP_NUM'] ?? 0) ?><?php else: ?>index.php?uc=rapport&amp;action=mesrapports<?php endif; ?>">
                                        <?= !empty($isViewing) ? 'Retour à la liste' : 'Retour' ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
    (() => {
        const liste = document.getElementById('echantillonsListe');
        const modele = document.getElementById('modeleEchantillon');
        const ajouter = document.getElementById('ajouterEchantillon');
        const compteur = document.getElementById('echantillonsCompteur');
        const lectureSeule = <?= !empty($isViewing) ? 'true' : 'false' ?>;
        if (!liste || !ajouter || !compteur) {
            return;
        }

        const synchroniserLignes = () => {
            const lignes = liste.querySelectorAll('.echantillon-ligne');
            const nombreProduits = Array.from(lignes).filter((ligne) =>
                ligne.querySelector('.echantillon-medicament').value !== ''
            ).length;
            lignes.forEach((ligne) => {
                ligne.querySelector('.retirer-echantillon').hidden = lectureSeule || lignes.length === 1;
            });
            compteur.textContent = nombreProduits + (nombreProduits > 1 ? ' produits' : ' produit');
        };

        liste.addEventListener('click', (event) => {
            const bouton = event.target.closest('.retirer-echantillon');
            if (bouton) {
                bouton.closest('.echantillon-ligne').remove();
                synchroniserLignes();
            }
        });

        const mettreAJourObligatoire = (event) => {
            if (event.target.matches('.echantillon-medicament, .echantillon-quantite')) {
                const ligne = event.target.closest('.echantillon-ligne');
                const medicament = ligne.querySelector('.echantillon-medicament');
                const quantite = ligne.querySelector('.echantillon-quantite');
                const saisieCommencee = medicament.value !== '' || quantite.value !== '';
                medicament.required = saisieCommencee;
                quantite.required = saisieCommencee;
                synchroniserLignes();
            }
        };
        liste.addEventListener('input', mettreAJourObligatoire);
        liste.addEventListener('change', mettreAJourObligatoire);

        ajouter.addEventListener('click', () => {
            const index = Number(liste.dataset.prochainIndex || liste.querySelectorAll('.echantillon-ligne').length);
            const ligne = modele.content.firstElementChild.cloneNode(true);
            ligne.innerHTML = ligne.innerHTML.split('__INDEX__').join(String(index));
            liste.appendChild(ligne);
            liste.dataset.prochainIndex = String(index + 1);
            if (lectureSeule) {
                ligne.querySelector('.echantillon-medicament').disabled = true;
                ligne.querySelector('.echantillon-quantite').readOnly = true;
            }
            synchroniserLignes();
            if (!lectureSeule) {
                ligne.querySelector('.echantillon-medicament').focus();
            }
        });

        const valeursExistantes = <?= json_encode(array_values($formValues['echantillons'] ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const premiereLigne = liste.querySelector('.echantillon-ligne');
        if (valeursExistantes.length > 0) {
            const select = premiereLigne.querySelector('.echantillon-medicament');
            const quantite = premiereLigne.querySelector('.echantillon-quantite');
            select.value = valeursExistantes[0].medicament || '';
            quantite.value = valeursExistantes[0].quantite || '';
            if (valeursExistantes[0].medicament || valeursExistantes[0].quantite) {
                select.required = true;
                quantite.required = true;
            }
            valeursExistantes.slice(1).forEach((echantillon) => {
                ajouter.click();
                const ligne = liste.lastElementChild;
                const selectAjoute = ligne.querySelector('.echantillon-medicament');
                const quantiteAjoutee = ligne.querySelector('.echantillon-quantite');
                selectAjoute.value = echantillon.medicament || '';
                quantiteAjoutee.value = echantillon.quantite || '';
                if (echantillon.medicament || echantillon.quantite) {
                    selectAjoute.required = true;
                    quantiteAjoutee.required = true;
                }
            });
        }

        synchroniserLignes();
    })();
</script>
