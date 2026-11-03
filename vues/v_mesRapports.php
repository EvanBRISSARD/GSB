<section class="bg-light">
    <div class="container pb-5">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Mes rapports de visite</h1>
            <p class="text text-center">Retrouvez les rapports que vous avez rédigés.</p>
        </div>

        <?php if (!empty($rapportError)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($rapportError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php elseif (empty($rapports)): ?>
            <div class="alert alert-info text-center" role="status">
                Vous n’avez encore enregistré aucun rapport de visite.
            </div>
        <?php else: ?>
            <div class="table-responsive bg-white border rounded-3 shadow-sm">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">N°</th>
                            <th scope="col">Date de visite</th>
                            <th scope="col">Praticien</th>
                            <th scope="col">Motif</th>
                            <th scope="col">Bilan</th>
                            <th scope="col">Médicaments / échantillons</th>
                            <th scope="col">État</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rapports as $rapport): ?>
                            <tr class="rapport-ligne" tabindex="0"
                                aria-label="Consulter le rapport numéro <?= (int) $rapport['RAP_NUM'] ?>"
                                data-url="index.php?uc=rapport&amp;action=voirrapport&amp;matricule=<?= rawurlencode((string) ($_SESSION['matricule'] ?? '')) ?>&amp;numero=<?= (int) $rapport['RAP_NUM'] ?>"
                                style="cursor: pointer">
                                <td>
                                    <a href="index.php?uc=rapport&amp;action=voirrapport&amp;matricule=<?= rawurlencode((string) ($_SESSION['matricule'] ?? '')) ?>&amp;numero=<?= (int) $rapport['RAP_NUM'] ?>">
                                        <?= htmlspecialchars((string) $rapport['RAP_NUM'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars((string) $rapport['RAP_DATEVISITE'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(trim(($rapport['PRA_PRENOM'] ?? '') . ' ' . ($rapport['PRA_NOM'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($rapport['libelle_motif'] ?? 'Non renseigné'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($rapport['RAP_BILAN'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($rapport['medicaments'] ?? 'Aucun'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if (($rapport['RAP_ETAT'] ?? '') === 'V'): ?>
                                        <span class="badge bg-success">Définitif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">En cours</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.querySelectorAll('.rapport-ligne').forEach((ligne) => {
        ligne.addEventListener('click', (event) => {
            if (!event.target.closest('a, button, input, select, textarea')) {
                window.location.href = ligne.dataset.url;
            }
        });
        ligne.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.location.href = ligne.dataset.url;
            }
        });
    });
</script>
