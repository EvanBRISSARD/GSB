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