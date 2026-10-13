<!-- Le modèle permet d'accéder aux données dans la base, c'est là que se font les requêtes SQL -->

<?php

include_once 'bd.inc.php';

/**
 * Récupère le nom d'un praticien
 */
function getAllNomPraticien()
{
    try {
        $pdo = connexionPDO();
        $req = "SELECT PRA_NUM, PRA_NOM, PRA_PRENOM FROM praticien ORDER BY PRA_NOM";
        return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur SQL (getAllNomPraticien) : " . $e->getMessage());
    }
}


/**
 * Récupère les infos d'un praticien
 */
function getAllInformationPraticien($num)
{
    try {
        $pdo = connexionPDO();


        // On récupère toutes les informations du praticien ainsi que le libellé de son type
        $req = "SELECT p.*, t.TYP_LIBELLE
                FROM praticien p
                LEFT JOIN type_praticien t ON p.TYP_CODE = t.TYP_CODE
                WHERE p.PRA_NUM = :num
                ORDER BY p.PRA_NUM";

        $stmt = $pdo->prepare($req);  // Requête préparée et paramétrée
        $stmt->bindValue(":num", $num, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur SQL (getAllInformationPraticien) : " . $e->getMessage());
    }
}



/**
 * Liste des types de praticien
 */
function getAllTypesPraticien()
{
    try {
        $pdo = connexionPDO();
        return $pdo->query("SELECT * FROM type_praticien ORDER BY TYP_LIBELLE")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur SQL (getAllTypesPraticien) : " . $e->getMessage());
    }
}



/**
 * Récupère tous les praticiens d'une région.
 * Utilisé pour les délégués régionaux.
 */
function getPraticiensDeLaRegion($regionSession)
{
    $pdo = connexionPDO();

    $sql = "SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
            FROM praticien p
            JOIN departement d 
                ON d.NoDEPT = CAST(LEFT(LPAD(p.PRA_CP, 5, '0'), 2) AS UNSIGNED)
            JOIN region r 
                ON r.REG_CODE = d.REG_CODE
            WHERE r.REG_CODE = :regCode
               OR r.REG_NOM  = :regNom
            ORDER BY p.PRA_NOM";

    $req = $pdo->prepare($sql);
    $req->bindValue(':regCode', $regionSession, PDO::PARAM_STR);
    $req->bindValue(':regNom', $regionSession, PDO::PARAM_STR);
    $req->execute();

    return $req->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Récupère le code région du collaborateur connecté
 */
function getCodeRegionDuCollaborateur($matricule)
{
    $pdo = connexionPDO();

    $sql = "SELECT REG_CODE
            FROM collaborateur
            WHERE COL_MATRICULE = :matricule";

    $req = $pdo->prepare($sql);
    $req->bindValue(':matricule', $matricule, PDO::PARAM_STR);
    $req->execute();

    return $req->fetchColumn();
}



/**
 * Récupère le code secteur du collaborateur connecté.
 * Si COL.SEC_CODE est vide, on le déduit à partir de sa région.
 */
function getCodeSecteurDuCollaborateur($matricule)
{
    $pdo = connexionPDO();

    $sql = "SELECT COALESCE(c.SEC_CODE, r.SEC_CODE) AS SEC_CODE
            FROM collaborateur c
            LEFT JOIN region r
                ON r.REG_CODE = c.REG_CODE
            WHERE c.COL_MATRICULE = :matricule";

    $req = $pdo->prepare($sql);
    $req->bindValue(':matricule', $matricule, PDO::PARAM_STR);
    $req->execute();

    return $req->fetchColumn();
}



/**
 * Récupère tous les praticiens d'un secteur.
 * Utilisé pour les responsables de secteur.
 */
function getPraticiensDuSecteur($secteurSession)
{
    $pdo = connexionPDO();

    $sql = "SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
            FROM praticien p
            JOIN departement d
                ON d.NoDEPT = CAST(LEFT(LPAD(p.PRA_CP, 5, '0'), 2) AS UNSIGNED)
            JOIN region r
                ON r.REG_CODE = d.REG_CODE
            JOIN secteur s
                ON s.SEC_CODE = r.SEC_CODE
            WHERE r.SEC_CODE = :secCode
               OR s.SEC_LIBELLE = :secLibelle
            ORDER BY p.PRA_NOM";

    // CAST permet de transformer les deux premiers caractères du code postal en nombre, pour ensuite les comparer au numéro dans la table departement
    // (NoDEPT) qui est un entier

    $req = $pdo->prepare($sql);
    $req->bindValue(':secCode', $secteurSession, PDO::PARAM_STR);
    $req->bindValue(':secLibelle', $secteurSession, PDO::PARAM_STR);
    $req->execute();

    return $req->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Récupère les praticiens accessibles dans l'écran de gestion selon le rôle.
 * Habilitation 2 : praticiens de la région du délégué.
 * Habilitation 3 : praticiens de tout le secteur du responsable.
 */
function getPraticiensAccessiblesGestion($matricule, $habilitation)
{
    if ((int) $habilitation === 3) {
        $secteur = getCodeSecteurDuCollaborateur($matricule);
        return getPraticiensDuSecteur($secteur);
    }

    $region = getCodeRegionDuCollaborateur($matricule);
    return getPraticiensDeLaRegion($region);
}



/**
 * Vérifie qu'un praticien appartient bien au périmètre de gestion du collaborateur.
 * Évite qu'un utilisateur modifie un praticien hors région/secteur en changeant l'URL.
 */
function praticienAccessibleGestion($num, $matricule, $habilitation)
{
    $pdo = connexionPDO();

    if ((int) $habilitation === 3) {
        $sql = "SELECT COUNT(*)
                FROM praticien p
                JOIN departement d
                    ON d.NoDEPT = CAST(LEFT(LPAD(p.PRA_CP, 5, '0'), 2) AS UNSIGNED)
                JOIN region rPraticien
                    ON rPraticien.REG_CODE = d.REG_CODE
                JOIN collaborateur c
                    ON c.COL_MATRICULE = :matricule
                LEFT JOIN region rCollaborateur
                    ON rCollaborateur.REG_CODE = c.REG_CODE
                WHERE p.PRA_NUM = :num
                  AND rPraticien.SEC_CODE = COALESCE(c.SEC_CODE, rCollaborateur.SEC_CODE)";
    } else {
        $sql = "SELECT COUNT(*)
                FROM praticien p
                JOIN departement d
                    ON d.NoDEPT = CAST(LEFT(LPAD(p.PRA_CP, 5, '0'), 2) AS UNSIGNED)
                JOIN region rPraticien
                    ON rPraticien.REG_CODE = d.REG_CODE
                JOIN collaborateur c
                    ON c.COL_MATRICULE = :matricule
                WHERE p.PRA_NUM = :num
                  AND rPraticien.REG_CODE = c.REG_CODE";
    }

    $req = $pdo->prepare($sql);
    $req->bindValue(':matricule', $matricule, PDO::PARAM_STR);
    $req->bindValue(':num', $num, PDO::PARAM_INT);
    $req->execute();

    return $req->fetchColumn() > 0;
}



/**
 * Mise à jour du praticien
 */
function updatePraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $coefConfiance, $type)
{
    try {
        $pdo = connexionPDO();

        $req = "UPDATE praticien SET 
                PRA_NOM = :nom,
                PRA_PRENOM = :prenom,
                PRA_ADRESSE = :adresse,
                PRA_CP = :cp,
                PRA_VILLE = :ville,
                PRA_COEFNOTORIETE = :coef,
                PRA_COEFCONFIANCE = :coefConfiance,
                TYP_CODE = :type
                WHERE PRA_NUM = :num AND PRA_CP = :ancienCp";

        // oldCp = ancien CP du praticien pour empêcher le changement de région
        $stmt = $pdo->prepare($req);

        $stmt->execute([
            ":nom" => $nom,
            ":prenom" => $prenom,
            ":adresse" => $adresse,
            ":cp" => $cp,
            ":ville" => $ville,
            ":coef" => $coef,
            ":coefConfiance" => $coefConfiance,
            ":type" => $type,
            ":num" => $num,
            ":ancienCp" => getAncienCpPraticien($num)
        ]);

        // Si aucune ligne modifiée, retourne false
        return ($stmt->rowCount() > 0);

    } catch (PDOException $e) {
        die("Erreur SQL (updatePraticien) : " . $e->getMessage());
    }
}



/**
 * Permet de récupérer l'ancien code postal du praticien pour éviter de changer le praticien de région
 */
function getAncienCpPraticien($num)
{
    $pdo = connexionPDO();
    $stmt = $pdo->prepare("SELECT PRA_CP FROM praticien WHERE PRA_NUM = :num");
    $stmt->execute([":num" => $num]);
    return $stmt->fetchColumn();
}



/**
 * Permet de récupérer toutes les spécialités
 */
function getAllSpecialites()
{
    $pdo = connexionPDO();
    return $pdo->query("SELECT * FROM specialite ORDER BY SPE_LIBELLE")->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Récupère les spécialités d’un praticien
 */
function getSpecialitesPraticien($num)
{
    $pdo = connexionPDO();
    $stmt = $pdo->prepare("SELECT SPE_CODE FROM posseder WHERE PRA_NUM = :num");  // Requête préparée paramétrée
    $stmt->execute([":num" => $num]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}



/**
 * Met à jour les spécialités d’un praticien
 */
function updateSpecialitesPraticien($num, $specialites)
{
    // Connexion à la base de données
    $pdo = connexionPDO();

    // Récupération des spécialités actuellement associées au praticien
    $stmt = $pdo->prepare("SELECT SPE_CODE FROM posseder WHERE PRA_NUM = :num");
    $stmt->execute([":num" => $num]);
    $anciennes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Si aucune spécialité, tableaux vides
    $anciennes = $anciennes ?? [];
    $specialites = $specialites ?? [];

    // Tri des tableaux pour pouvoir les comparer correctement
    sort($anciennes);
    sort($specialites);

    // Si les anciennes spécialités sont identiques aux nouvelles, aucune modification n’est nécessaire
    if ($anciennes === $specialites) {
        return false; // indique qu’il n’y a eu aucun changement
    }

    // Suppression de toutes les anciennes spécialités du praticien avant d'insérer les nouvelles
    $pdo->prepare("DELETE FROM posseder WHERE PRA_NUM = :num")
        ->execute([":num" => $num]);

    // Requête d’insertion des nouvelles spécialités
    $insert = $pdo->prepare("
        INSERT INTO posseder (SPE_CODE, PRA_NUM, POS_DIPLOME, POS_COEFPRESCRIPTIO) 
        VALUES (:spe, :num, '', 0)
    ");

    // Insertion de chaque spécialité sélectionnée
    foreach ($specialites as $s) {
        $insert->execute([
            ":spe" => $s,
            ":num" => $num
        ]);
    }

    // Retourne true pour indiquer qu’une modification a bien eu lieu
    return true;
}



/**
 * Récupère les praticiens liés à la région du collaborateur.
 */
function getPraticiensDuCollaborateur($matricule)
{
    $regionCode = getCodeRegionDuCollaborateur($matricule);

    return getPraticiensDeLaRegion($regionCode);
}



/**
 * Insère un nouveau praticien dans la base de données grâce au formulaire de création
 */
function insererPraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $coefConfiance, $type)
{
    try {
        $pdo = connexionPDO();

        $sql = "INSERT INTO praticien (PRA_NUM, PRA_NOM, PRA_PRENOM, PRA_ADRESSE, PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, PRA_COEFCONFIANCE, TYP_CODE) VALUES
                (:num, :nom, :prenom, :adresse, :cp, :ville, :coef, :coefConfiance, :type)";

        $stmt = $pdo->prepare($sql);  // Requête préparée et paramétrée

        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $stmt->bindValue(':cp', $cp, PDO::PARAM_STR);
        $stmt->bindValue(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindValue(':coef', $coef, PDO::PARAM_STR);
        $stmt->bindValue(':coefConfiance', $coefConfiance, PDO::PARAM_STR);

        $stmt->bindValue(':type', $type, is_null($type) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();

    } catch (PDOException $e) {
        die("Erreur SQL (insererPraticien) : " . $e->getMessage());
    }
}



/**
 * Insère les spécialités d’un praticien dans la base de données grâce au formulaire de création
 */
function insererSpecialitesPraticien($num, $specialites)
{
    $pdo = connexionPDO();

    if (!is_array($specialites))
        return;

    foreach ($specialites as $spe) {

        // On insère chaque spécialité du praticien
        $sql = "INSERT INTO posseder (SPE_CODE, PRA_NUM, POS_DIPLOME, POS_COEFPRESCRIPTIO) VALUES
                (:spe, :num, '', 0)";

        // On insère la spécialité au praticien souhaité
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':spe' => $spe,
            ':num' => $num
        ]);
    }
}



/**
 * Récupère le prochain numéro de praticien disponible (si dans la base, le dernier praticien a le numéro 86, on renvoie 87 pour pouvoir créer le praticien 87)
 */
function getPraticienNum()
{
    $pdo = connexionPDO();

    $sql = "SELECT MAX(PRA_NUM) AS maxNum
            FROM praticien";

    $req = $pdo->query($sql);
    $row = $req->fetch(PDO::FETCH_ASSOC);

    // Si aucune ligne en bdd, renvoyer 1, le praticien sera le premier à être créé
    return ($row['maxNum'] !== null) ? $row['maxNum'] + 1 : 1;
}



/**
 * Indique si un praticien existe dans la base
 */
function praticienExiste($num)
{
    try {
        $pdo = connexionPDO();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM praticien WHERE PRA_NUM = :num");
        $stmt->execute([":num" => $num]);

        // S'il y a au moins un praticien dans la bdd
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        die("Erreur SQL (praticienExiste) : " . $e->getMessage());
    }
}
?>