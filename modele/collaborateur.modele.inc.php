

<?php 
include_once 'bd.inc.php';

    function getAllCollaborateur(): array{
        try
        {
            $monPdo = connexionPDO();
            $req = 'SELECT * FROM collaborateur';
            return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
        } 
    
        catch (PDOException $e){
            print "Erreur !: " . $e->getMessage();
            die();
        }
    }

    function getAllHabilitation(): array{
        try
        {
            $monPdo = connexionPDO();
            $req = 'SELECT * FROM habilitation';
			return $pdo->query($req)->fetchAll(PDO::FETCH_ASSOC);
        } 
    
        catch (PDOException $e){
            print "Erreur !: " . $e->getMessage();
            die();
        }
    }


    function getLibelleHabilitation(int $hab_id): array{
		
		
    try {
        $pdo = connexionPDO();

        $req ="SELECT hab_lib FROM habilitation WHERE hab_id = :hab_id";
            $stmt = $pdo->prepare($req);
			$stmt->bindValue("hab_id", $hab_id, PDO::PARAM_INT);
			$stmt->execute();
            return $req->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        die("Erreur SQL (connexion) : " . $e->getMessage());
    }
    
	}

?>