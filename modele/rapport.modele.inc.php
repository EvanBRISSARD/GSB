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

function getRapportsDuCollaborateur(string $matricule): array
{
    $pdo = connexionPDO();
    $req = $pdo->prepare(
        "SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_DATESAISIE, rv.RAP_BILAN, rv.RAP_ETAT,
                p.PRA_NOM, p.PRA_PRENOM, mv.libelle_motif,
                remp.PRA_NOM AS remplacant_nom, remp.PRA_PRENOM AS remplacant_prenom,
                COALESCE(
                    NULLIF(GROUP_CONCAT(DISTINCT CONCAT(m.MED_NOMCOMMERCIAL, ' (', o.off_qte, ')')
                        ORDER BY m.MED_NOMCOMMERCIAL SEPARATOR ', '), ''),
                    NULLIF(CONCAT_WS(', ', m1.MED_NOMCOMMERCIAL, m2.MED_NOMCOMMERCIAL), '')
                ) AS medicaments
         FROM rapport_visite rv
         LEFT JOIN praticien p ON p.PRA_NUM = rv.PRA_NUM
         LEFT JOIN praticien remp ON remp.PRA_NUM = rv.PRA_NUM_REMPLACANT
         LEFT JOIN motif_visite mv ON mv.id_motif = rv.id_motif
         LEFT JOIN medicament m1 ON m1.MED_DEPOTLEGAL = rv.MED_DEPOTLEGAL_1
         LEFT JOIN medicament m2 ON m2.MED_DEPOTLEGAL = rv.MED_DEPOTLEGAL_2
         LEFT JOIN offrir o ON o.COL_MATRICULE = rv.COL_MATRICULE AND o.RAP_NUM = rv.RAP_NUM
         LEFT JOIN medicament m ON m.MED_DEPOTLEGAL = o.MED_DEPOTLEGAL
         WHERE rv.COL_MATRICULE = :matricule
         GROUP BY rv.COL_MATRICULE, rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_DATESAISIE,
                  rv.RAP_BILAN, rv.RAP_ETAT, p.PRA_NOM, p.PRA_PRENOM, mv.libelle_motif,
                  remp.PRA_NOM, remp.PRA_PRENOM, m1.MED_NOMCOMMERCIAL, m2.MED_NOMCOMMERCIAL
         ORDER BY rv.RAP_DATEVISITE DESC, rv.RAP_NUM DESC"
    );
    $req->execute([':matricule' => $matricule]);

    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportsDeLaRegion(string $matricule): array
{
    $pdo = connexionPDO();
    $req = $pdo->prepare(
        "SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_DATESAISIE, rv.RAP_BILAN, rv.RAP_ETAT,
                c.COL_MATRICULE, c.COL_NOM AS collaborateur_nom, c.COL_PRENOM AS collaborateur_prenom,
                p.PRA_NOM, p.PRA_PRENOM, mv.libelle_motif,
                COALESCE(
                    NULLIF(GROUP_CONCAT(DISTINCT CONCAT(m.MED_NOMCOMMERCIAL, ' (', o.off_qte, ')')
                        ORDER BY m.MED_NOMCOMMERCIAL SEPARATOR ', '), ''),
                    NULLIF(CONCAT_WS(', ', m1.MED_NOMCOMMERCIAL, m2.MED_NOMCOMMERCIAL), '')
                ) AS medicaments
         FROM rapport_visite rv
         INNER JOIN collaborateur c ON c.COL_MATRICULE = rv.COL_MATRICULE
         INNER JOIN collaborateur connecte ON connecte.COL_MATRICULE = :matricule
             AND connecte.REG_CODE = c.REG_CODE
         LEFT JOIN praticien p ON p.PRA_NUM = rv.PRA_NUM
         LEFT JOIN motif_visite mv ON mv.id_motif = rv.id_motif
         LEFT JOIN medicament m1 ON m1.MED_DEPOTLEGAL = rv.MED_DEPOTLEGAL_1
         LEFT JOIN medicament m2 ON m2.MED_DEPOTLEGAL = rv.MED_DEPOTLEGAL_2
         LEFT JOIN offrir o ON o.COL_MATRICULE = rv.COL_MATRICULE AND o.RAP_NUM = rv.RAP_NUM
         LEFT JOIN medicament m ON m.MED_DEPOTLEGAL = o.MED_DEPOTLEGAL
         GROUP BY rv.COL_MATRICULE, rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_DATESAISIE,
                  rv.RAP_BILAN, rv.RAP_ETAT, c.COL_MATRICULE, c.COL_NOM, c.COL_PRENOM,
                  p.PRA_NOM, p.PRA_PRENOM, mv.libelle_motif, m1.MED_NOMCOMMERCIAL, m2.MED_NOMCOMMERCIAL
         ORDER BY rv.RAP_DATEVISITE DESC, c.COL_NOM, c.COL_PRENOM, rv.RAP_NUM DESC"
    );
    $req->execute([':matricule' => $matricule]);

    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportVisite(string $proprietaire, int $numero, string $lecteur, int $habilitation): ?array
{
    $pdo = connexionPDO();
    $req = $pdo->prepare(
        "SELECT rv.*, c.COL_NOM AS collaborateur_nom, c.COL_PRENOM AS collaborateur_prenom,
                p.PRA_NOM, p.PRA_PRENOM, mv.libelle_motif,
                remp.PRA_NOM AS remplacant_nom, remp.PRA_PRENOM AS remplacant_prenom
         FROM rapport_visite rv
         INNER JOIN collaborateur c ON c.COL_MATRICULE = rv.COL_MATRICULE
         INNER JOIN collaborateur lecteur ON lecteur.COL_MATRICULE = :lecteur
         LEFT JOIN praticien p ON p.PRA_NUM = rv.PRA_NUM
         LEFT JOIN praticien remp ON remp.PRA_NUM = rv.PRA_NUM_REMPLACANT
         LEFT JOIN motif_visite mv ON mv.id_motif = rv.id_motif
         WHERE rv.COL_MATRICULE = :proprietaire
           AND rv.RAP_NUM = :numero
           AND (
               rv.COL_MATRICULE = :lecteurProprietaire
               OR (:habilitation = 2 AND c.REG_CODE = lecteur.REG_CODE)
           )"
    );
    $req->execute([
        ':lecteur' => $lecteur,
        ':proprietaire' => $proprietaire,
        ':numero' => $numero,
        ':lecteurProprietaire' => $lecteur,
        ':habilitation' => $habilitation,
    ]);
    $rapport = $req->fetch(PDO::FETCH_ASSOC);

    return $rapport ?: null;
}

function getEchantillonsRapport(string $matricule, int $numero): array
{
    $pdo = connexionPDO();
    $req = $pdo->prepare(
        'SELECT o.MED_DEPOTLEGAL AS medicament, m.MED_NOMCOMMERCIAL AS nom, o.off_qte AS quantite
         FROM offrir o
         INNER JOIN medicament m ON m.MED_DEPOTLEGAL = o.MED_DEPOTLEGAL
         WHERE o.COL_MATRICULE = :matricule AND o.RAP_NUM = :numero
         ORDER BY m.MED_NOMCOMMERCIAL'
    );
    $req->execute([':matricule' => $matricule, ':numero' => $numero]);
    $echantillons = $req->fetchAll(PDO::FETCH_ASSOC);

    if ($echantillons !== []) {
        return $echantillons;
    }

    $req = $pdo->prepare(
        'SELECT MED_DEPOTLEGAL_1, MED_DEPOTLEGAL_2
         FROM rapport_visite
         WHERE COL_MATRICULE = :matricule AND RAP_NUM = :numero'
    );
    $req->execute([':matricule' => $matricule, ':numero' => $numero]);
    $medicaments = $req->fetch(PDO::FETCH_ASSOC);
    if (!$medicaments) {
        return [];
    }

    return array_values(array_filter([
        $medicaments['MED_DEPOTLEGAL_1'] ? ['medicament' => $medicaments['MED_DEPOTLEGAL_1'], 'nom' => null, 'quantite' => null] : null,
        $medicaments['MED_DEPOTLEGAL_2'] ? ['medicament' => $medicaments['MED_DEPOTLEGAL_2'], 'nom' => null, 'quantite' => null] : null,
    ]));
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

function modifierRapportVisite(string $matricule, int $numero, array $rapport, array $echantillons): void
{
    $pdo = connexionPDO();
    $pdo->beginTransaction();

    try {
        $reqEtat = $pdo->prepare(
            'SELECT RAP_ETAT FROM rapport_visite
             WHERE COL_MATRICULE = :matricule AND RAP_NUM = :numero
             FOR UPDATE'
        );
        $reqEtat->execute([':matricule' => $matricule, ':numero' => $numero]);
        $etat = $reqEtat->fetchColumn();
        if ($etat === false) {
            throw new InvalidArgumentException('Ce rapport est introuvable ou ne vous appartient pas.');
        }
        if ($etat !== 'E') {
            throw new InvalidArgumentException('Ce rapport est définitif et ne peut plus être modifié.');
        }

        $medicaments = array_values($echantillons);
        $reqRapport = $pdo->prepare(
            'UPDATE rapport_visite
             SET RAP_DATEVISITE = :date_visite, RAP_BILAN = :bilan, RAP_ETAT = :etat,
                 PRA_NUM = :praticien, id_motif = :motif, PRA_NUM_REMPLACANT = :remplacant,
                 MED_DEPOTLEGAL_1 = :medicament1, MED_DEPOTLEGAL_2 = :medicament2
             WHERE COL_MATRICULE = :matricule AND RAP_NUM = :numero'
        );
        $reqRapport->execute([
            ':date_visite' => $rapport['date_visite'],
            ':bilan' => $rapport['bilan'],
            ':etat' => $rapport['definitif'] ? 'V' : 'E',
            ':praticien' => $rapport['praticien'],
            ':motif' => $rapport['motif'],
            ':remplacant' => $rapport['remplacant'],
            ':medicament1' => $medicaments[0]['medicament'] ?? null,
            ':medicament2' => $medicaments[1]['medicament'] ?? null,
            ':matricule' => $matricule,
            ':numero' => $numero,
        ]);

        $reqSupprimerEchantillons = $pdo->prepare(
            'DELETE FROM offrir WHERE COL_MATRICULE = :matricule AND RAP_NUM = :numero'
        );
        $reqSupprimerEchantillons->execute([':matricule' => $matricule, ':numero' => $numero]);

        $reqEchantillon = $pdo->prepare(
            'INSERT INTO offrir (MED_DEPOTLEGAL, COL_MATRICULE, RAP_NUM, off_qte)
             VALUES (:medicament, :matricule, :numero, :quantite)'
        );
        foreach ($echantillons as $echantillon) {
            $reqEchantillon->execute([
                ':medicament' => $echantillon['medicament'],
                ':matricule' => $matricule,
                ':numero' => $numero,
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
