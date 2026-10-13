<?php

include_once 'bd.inc.php';

/**
 * Récupère les infos du collaborateur
 */
 function getAllInformationCompte($matricule)
{
    try {
		$pdo = connexionPDO();
		$req= "SELECT c.COL_MATRICULE as matricule,c.COL_NOM as nom,COL_PRENOM as prenom,
		c.COL_ADRESSE as adresse,
		c.COL_CP as cp,c.COL_VILLE as ville,  h.HAB_LIB as habilitation ,s.SEC_LIBELLE as secteur, r.REG_NOM as region,
		h.HAB_ID as libID FROM collaborateur c 
		LEFT JOIN habilitation h ON h.HAB_ID=c.HAB_ID LEFT JOIN region r ON r.REG_CODE=c.REG_CODE 
		LEFT JOIN secteur s ON s.SEC_CODE=r.SEC_CODE 
		WHERE c.COL_MATRICULE= :matricule";
		$stmt = $pdo->prepare($req);  // Requête préparée et paramétrée
        $stmt->bindValue(":matricule", $matricule, PDO::PARAM_INT);
        $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}


function checkConnexion($username, $mdp)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('SELECT l.LOG_ID as \'id_log\', l.COL_MATRICULE as \'matricule\', c.HAB_ID as \'habilitation\' 
		FROM login l INNER JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE WHERE l.LOG_LOGIN = :identifiant 
		AND l.LOG_MOTDEPASSE = "' . hash('sha512', $mdp) . '"');
        $req->bindParam(':identifiant', $username, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatriculeInscription($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from login where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatricule($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from collaborateur where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkUserInscription($username)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('SELECT `LOG_LOGIN` from login where `LOG_LOGIN`=:username');
        $req->bindParam(':username', $username, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

