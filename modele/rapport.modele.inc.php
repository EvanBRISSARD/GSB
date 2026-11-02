<?php

include_once __DIR__ . '/bd.inc.php';

function getAllMotifsVisite(): array
{
    try {
        $pdo = connexionPDO();
        $req = 'SELECT id_motif, libelle_motif FROM motif_visite ORDER BY id_motif';
        return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur SQL (getAllMotifsVisite) : ' . $e->getMessage());
    }
}

function getAllMedicamentsPourRapport(): array
{
    try {
        $pdo = connexionPDO();
        $req = 'SELECT MED_DEPOTLEGAL, MED_NOMCOMMERCIAL FROM medicament ORDER BY MED_NOMCOMMERCIAL';
        return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur SQL (getAllMedicamentsPourRapport) : ' . $e->getMessage());
    }
}

function enregistrerRapportVisite(array $rapport, array $echantillons): void
{
    $pdo = connexionPDO();
    $pdo->beginTransaction();

    try {
        $medicaments = array_values($echantillons);
        $medicament1 = $medicaments[0]['medicament'] ?? null;
        $medicament2 = $medicaments[1]['medicament'] ?? null;

        $reqRapport = $pdo->prepare(
            'INSERT INTO rapport_visite
                (COL_MATRICULE, RAP_NUM, RAP_DATEVISITE, RAP_BILAN, RAP_DATESAISIE,
                 RAP_ETAT, PRA_NUM, id_motif, PRA_NUM_REMPLACANT,
                 MED_DEPOTLEGAL_1, MED_DEPOTLEGAL_2)
             VALUES
                (:matricule, :numero, :date_visite, :bilan, :date_saisie,
                 :etat, :praticien, :motif, :remplacant, :medicament1, :medicament2)'
        );
        $reqRapport->execute([
            ':matricule' => $rapport['matricule'],
            ':numero' => $rapport['numero'],
            ':date_visite' => $rapport['date_visite'],
            ':bilan' => $rapport['bilan'],
            ':date_saisie' => date('Y-m-d'),
            ':etat' => $rapport['definitif'] ? 'V' : 'E',
            ':praticien' => $rapport['praticien'],
            ':motif' => $rapport['motif'],
            ':remplacant' => $rapport['remplacant'],
            ':medicament1' => $medicament1,
            ':medicament2' => $medicament2,
        ]);

        $reqEchantillon = $pdo->prepare(
            'INSERT INTO offrir (MED_DEPOTLEGAL, COL_MATRICULE, RAP_NUM, off_qte)
             VALUES (:medicament, :matricule, :numero, :quantite)'
        );
        foreach ($echantillons as $echantillon) {
            $reqEchantillon->execute([
                ':medicament' => $echantillon['medicament'],
                ':matricule' => $rapport['matricule'],
                ':numero' => $rapport['numero'],
                ':quantite' => $echantillon['quantite'],
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
