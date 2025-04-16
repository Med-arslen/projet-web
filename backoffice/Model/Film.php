<?php
class Film {
    private $id_film;
    private $titre;
    private $genre;
    private $annee_sortie;
    private $duree;
    private $age_recommande;

    public function __construct( $id_film= Null, $titre= Null, $genre= Null, $annee_sortie= Null, $duree= Null, $age_recommande= Null) {
        $this->id_film = $id_film;
        $this->titre = $titre;
        $this->genre = $genre;
        $this->annee_sortie = $annee_sortie;
        $this->duree = $duree;
        $this->age_recommande = $age_recommande;
    }

    // --- GETTERS ---
    public function getId() { return $this->id_film; }
    public function getTitre() { return $this->titre; }
    public function getGenre() { return $this->genre; }
    public function getAnneeSortie() { return $this->annee_sortie; }
    public function getDuree() { return $this->duree; }
    public function getAgeRecommande() { return $this->age_recommande; }

    // --- SETTERS ---
    public function setId($id) { $this->id_film = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setGenre($genre) { $this->genre = $genre; }
    public function setAnneeSortie($annee) { $this->annee_sortie = $annee; }
    public function setDuree($duree) { $this->duree = $duree; }
    public function setAgeRecommande($age) { $this->age_recommande = $age; }
}
?>
