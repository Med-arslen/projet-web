<?php
include_once __DIR__ . '/../Model/feedback.php';
include_once __DIR__ . '/../config/database.php';

class feedbackController {

    public function showFeedback($id) {
        $sql = "SELECT * FROM feedbackk WHERE id_fed = :id_fed";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id_fed' => $id]);
            $feedback = $query->fetch(PDO::FETCH_ASSOC);
            return $feedback;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function listFeedback() {
        $sql = "SELECT * FROM feedbackk";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $liste = $query->fetchAll(PDO::FETCH_ASSOC);
            return $liste;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addFeedback($feedbackk) {
        $sql = "INSERT INTO feedbackk (id_fed, id_rec, analyse, conseil, simplicite, temps)
                VALUES (:id_fed, :id_rec, :analyse, :conseil, :simplicite, :temps)";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $success = $query->execute([
                'id_fed'    => $feedbackk->getIdFed(),
                'id_rec'    => $feedbackk->getIdRec(),
                'analyse'   => $feedbackk->getAnalyse(),
                'conseil'   => $feedbackk->getConseil(),
                'simplicite'=> $feedbackk->getSimplicite(), // Ajout du champ simplicite
                'temps'     => $feedbackk->getTemps() // Ajout du champ temps
            ]);
            
            if ($success) {
                echo "Feedback ajouté avec succès!";
            } else {
                echo "L'insertion a échoué.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateFeedback($feedbackk, $id) {
        $sql = "UPDATE feedbackk SET 
                    analyse = :analyse,
                    conseil = :conseil,
                    id_rec = :id_rec,
                    simplicite = :simplicite,  -- Mise à jour du champ 'simplicite'
                    temps = :temps             -- Mise à jour du champ 'temps'
                WHERE id_fed = :id_fed";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_fed'    => $id,
                'analyse'   => $feedbackk->getAnalyse(),
                'conseil'   => $feedbackk->getConseil(),
                'id_rec'    => $feedbackk->getIdRec(),
                'simplicite'=> $feedbackk->getSimplicite(),  // Envoi du champ 'simplicite'
                'temps'     => $feedbackk->getTemps()         // Envoi du champ 'temps'
            ]);
            echo $query->rowCount() . " feedbackk updated successfully.<br>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteFeedback($id_rec) {
        $sql = "DELETE FROM feedbackk WHERE id_rec = :id_rec";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_rec', $id_rec, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Nouvelle fonction pour obtenir les statistiques sur le temps
    public function getTempsStats() {
        $sql = "SELECT temps, COUNT(*) as count FROM feedbackk GROUP BY temps";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $stats = [
                'rapide' => 0,
                'moyen' => 0,
                'lent' => 0
            ];

            // Remplir les statistiques avec les résultats de la requête
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $temps = $row['temps'];
                if (isset($stats[$temps])) {
                    $stats[$temps] = $row['count'];
                }
            }

            return $stats;

        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}
?>
