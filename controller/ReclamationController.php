<?php
include_once __DIR__ . '/../Model/Reclamation.php';
include_once __DIR__ . '/../config/database.php';

class ReclamationController {

    // Afficher une réclamation
    public function showReclamation($id)
    {
        // Vérification si l'ID est passé et valide
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        $sql = "SELECT * FROM reclamationn WHERE id_rec = :id_rec";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id_rec' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de l'affichage de la réclamation : " . $e->getMessage());
            die('Erreur lors de la récupération de la réclamation.');
        }
    }

    // Lister toutes les réclamations
    public function listReclamations()
    {
        $sql = "SELECT * FROM reclamationn";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des réclamations : " . $e->getMessage());
            die('Erreur lors de la récupération des réclamations.');
        }
    }

    // Mettre à jour une réclamation
    public function updateReclamation($reclamationn, $id)
    {
        // Vérification si l'ID est valide
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        try {
            // Validation des données avant de les exécuter
            if (empty($reclamationn->getNomPrenom()) || empty($reclamationn->getEmail()) || empty($reclamationn->getNomFilm()) || empty($reclamationn->getTypeRec()) || empty($reclamationn->getDetail()) || empty($reclamationn->getReponseRec())) {
                throw new Exception("Tous les champs doivent être remplis.");
            }

            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE reclamationn SET 
                    nomprenom = :nomprenom,
                    email = :email,
                    nomfilm = :nomfilm,
                    type_rec = :type_rec,
                    detail = :detail,
                    reponse_rec = :reponse_rec
                WHERE id_rec = :id_rec'
            );

            $query->execute([ 
                'id_rec'    => $id,
                'nomprenom' => $reclamationn->getNomPrenom(),
                'email'     => $reclamationn->getEmail(),
                'nomfilm'   => $reclamationn->getNomFilm(),
                'type_rec'  => $reclamationn->getTypeRec(),
                'detail'    => $reclamationn->getDetail(),
                'reponse_rec'    => $reclamationn->getReponseRec()
            ]);

            // Message de succès
            echo "Réclamation mise à jour avec succès.";
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la réclamation : " . $e->getMessage());
            echo "Erreur lors de la mise à jour de la réclamation : " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Ajouter une nouvelle réclamation
   // Ajouter une nouvelle réclamation
   public function addReclamation($reclamationn)
   {
       $sql = "INSERT INTO reclamationn (nomprenom,email,nomfilm,type_rec, detail, reponse_rec)
               VALUES (:nomprenom, :email, :nomfilm, :type_rec, :detail , :reponse_rec)";
   
       $db = config::getConnexion();
   
       try {
           $query = $db->prepare($sql);
           $query->execute([
               'nomprenom'   => $reclamationn->getNomPrenom(),
               'email'       => $reclamationn->getEmail(),
               'nomfilm'     => $reclamationn->getNomFilm(),
               'type_rec'    => $reclamationn->getTypeRec(),
               'detail'      => $reclamationn->getDetail(),
               'reponse_rec' => $reclamationn->getReponseRec(),
           ]);
           return $db->lastInsertId(); // <-- retourne l'ID ici
       } catch (PDOException $e) {
           echo "Error: " . $e->getMessage();
           return false;
       }
   }
   


    // Supprimer une réclamation
    public function deleteReclamation($id)
    {
        // Vérification si l'ID est valide
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        $sql = "DELETE FROM reclamationn WHERE id_rec = :id_rec";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_rec', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo "Réclamation supprimée avec succès.";
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la réclamation : " . $e->getMessage());
            die('Erreur de suppression : ' . $e->getMessage());
        }
    }

    public function getReclamationsSorted($sort_order = 'DESC')
    {
        // Connexion à la base de données avec PDO
        $db = config::getConnexion();
    
        // Création de la requête SQL pour trier par ordre décroissant ou ascendant selon le paramètre
        $sql = "SELECT * FROM reclamationn ORDER BY id_rec " . strtoupper($sort_order);
    
        try {
            // Préparer et exécuter la requête
            $query = $db->prepare($sql);
            $query->execute();
    
            // Récupérer et retourner les résultats sous forme de tableau associatif
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log l'erreur et affiche un message détaillé si la requête échoue
            error_log("Erreur lors de la récupération des réclamations triées : " . $e->getMessage());
            die('Erreur lors de la récupération des réclamations triées. Détails : ' . $e->getMessage());
        }
    }
    public static function getStatistiquesTypeRec($conn)
    {
        $sql = "SELECT type_rec, COUNT(*) as total FROM reclamationn GROUP BY type_rec";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    }
    
    
    

?>
