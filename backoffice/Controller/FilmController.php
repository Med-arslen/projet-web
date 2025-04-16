<?php
include 'C:\xampp\htdocs\backoffice\Model\Film.php';
include 'C:\xampp\htdocs\backoffice\config.php';


class FilmController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function createFilm($titre, $genre, $annee_sortie, $duree, $age_recommande) {
        $stmt = $this->pdo->prepare("INSERT INTO film (titre, genre, annee_sortie, duree, age_recommande) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$titre, $genre, $annee_sortie, $duree, $age_recommande]);
    }

    // Read all
    public function getAllFilms() {
        $stmt = $this->pdo->query("SELECT * FROM film");
        $films = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $films[] = new Film($row['id_film'], $row['titre'], $row['genre'], $row['annee_sortie'], $row['duree'], $row['age_recommande']);
        }
        return $films;
    }

    // Read one
    public function getFilmById($id_film) {
        $stmt = $this->pdo->prepare("SELECT * FROM film WHERE id_film = ?");
        $stmt->execute([$id_film]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Film($row['id_film'], $row['titre'], $row['genre'], $row['annee_sortie'], $row['duree'], $row['age_recommande']);
        }
        return null;
    }

    // Update
    public function updateFilm($id_film, $titre, $genre, $annee_sortie, $duree, $age_recommande) {
        $stmt = $this->pdo->prepare("UPDATE film SET titre = ?, genre = ?, annee_sortie = ?, duree = ?, age_recommande = ? WHERE id_film = ?");
        return $stmt->execute([$titre, $genre, $annee_sortie, $duree, $age_recommande, $id_film]);
    }

    // Delete
    public function deleteFilm($id_film) {
        $stmt = $this->pdo->prepare("DELETE FROM film WHERE id_film = ?");
        return $stmt->execute([$id_film]);
    }
}
?>
