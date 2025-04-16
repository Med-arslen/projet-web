<?php
include 'models/Commentaire.php';
include 'config/database.php';

class CommentaireController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function createCommentaire($id_film, $auteur, $contenu, $note, $date_commentaire) {
        $stmt = $this->pdo->prepare("INSERT INTO commentaire (id_film, auteur, contenu, note, date_commentaire) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id_film, $auteur, $contenu, $note, $date_commentaire]);
    }

    // Read all
    public function getAllCommentaires() {
        $stmt = $this->pdo->query("SELECT * FROM commentaire");
        $commentaires = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commentaires[] = new Commentaire($row['id_commentaire'], $row['id_film'], $row['auteur'], $row['contenu'], $row['note'], $row['date_commentaire']);
        }
        return $commentaires;
    }

    // Read by ID
    public function getCommentaireById($id_commentaire) {
        $stmt = $this->pdo->prepare("SELECT * FROM commentaire WHERE id_commentaire = ?");
        $stmt->execute([$id_commentaire]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Commentaire($row['id_commentaire'], $row['id_film'], $row['auteur'], $row['contenu'], $row['note'], $row['date_commentaire']);
        }
        return null;
    }

    // Update
    public function updateCommentaire($id_commentaire, $auteur, $contenu, $note, $date_commentaire) {
        $stmt = $this->pdo->prepare("UPDATE commentaire SET auteur = ?, contenu = ?, note = ?, date_commentaire = ? WHERE id_commentaire = ?");
        return $stmt->execute([$auteur, $contenu, $note, $date_commentaire, $id_commentaire]);
    }

    // Delete
    public function deleteCommentaire($id_commentaire) {
        $stmt = $this->pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = ?");
        return $stmt->execute([$id_commentaire]);
    }

    // Read by Film ID
    public function getCommentairesByFilm($id_film) {
        $stmt = $this->pdo->prepare("SELECT * FROM commentaire WHERE id_film = ?");
        $stmt->execute([$id_film]);
        $commentaires = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commentaires[] = new Commentaire($row['id_commentaire'], $row['id_film'], $row['auteur'], $row['contenu'], $row['note'], $row['date_commentaire']);
        }
        return $commentaires;
    }
}
?>
