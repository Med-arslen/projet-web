<?php
class HistoriqueController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addHistorique($actionType, $filmId, $filmTitre, $details) {
        try {
            $sql = "INSERT INTO historique_films (action_type, film_id, film_titre, details) 
                    VALUES (:action_type, :film_id, :film_titre, :details)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'action_type' => $actionType,
                'film_id' => $filmId,
                'film_titre' => $filmTitre,
                'details' => $details
            ]);
        } catch (PDOException $e) {
            error_log("Error adding historique: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentHistorique($limit = 10) {
        try {
            $sql = "SELECT * FROM historique_films ORDER BY date_action DESC LIMIT :limit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting historique: " . $e->getMessage());
            return [];
        }
    }
}