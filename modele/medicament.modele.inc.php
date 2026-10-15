<!-- Le modèle permet d'accéder aux données dans la base, c'est là que se font les requêtes SQL -->

<?php

include_once 'bd.inc.php';

function getAllNomMedicament()
{
    try {
        $pdo = connexionPDO();
        $req = "SELECT MED_DEPOTLEGAL, MED_NOMCOMMERCIAL FROM medicament ORDER BY MED_DEPOTLEGAL";
        return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur SQL (getAllNomPraticien) : " . $e->getMessage());
    }
}

function getAllInformationMedicament($num)
{
    try {
        $pdo = connexionPDO();


        // On récupère toutes les informations du praticien ainsi que le libellé de son type
        $req = "SELECT m.*, f.FAM_LIBELLE
                FROM medicament AS m
                INNER JOIN famille AS f
                    ON f.FAM_CODE = m.FAM_CODE
                WHERE m.MED_DEPOTLEGAL = :num;";

        $stmt = $pdo->prepare($req);  // Requête préparée et paramétrée
        $stmt->bindValue(":num", $num, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur SQL (getAllInformationMedicament) : " . $e->getMessage());
    }
}