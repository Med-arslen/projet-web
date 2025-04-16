<?php
class Commentaire {
    private $id_commentaire;
    private $id_film;
    private $auteur;
    private $contenu;
    private $note;
    private $date_commentaire;

    
    public function __construct( $id_commentaire= Null, $id_film= Null, $auteur= Null, $contenu= Null, $note= Null, $date_commentaire= Null) {
        $this->id_commentaire = $id_commentaire;
        $this->id_film = $id_film;
        $this->auteur = $auteur;
        $this->contenu = $contenu;
        $this->note = $note;
        $this->date_commentaire = $date_commentaire;
    }
    // --- GETTERS ---
    public function getIdCommentaire() { return $this->id_commentaire; }
    public function getIdFilm() { return $this->id_film; }
    public function getAuteur() { return $this->auteur; }
    public function getContenu() { return $this->contenu; }
    public function getNote() { return $this->note; }
    public function getDateCommentaire() { return $this->date_commentaire; }

    // --- SETTERS ---
    public function setIdCommentaire($id) { $this->id_commentaire = $id; }
    public function setIdFilm($id) { $this->id_film = $id; }
    public function setAuteur($auteur) { $this->auteur = $auteur; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setNote($note) { $this->note = $note; }
    public function setDateCommentaire($date) { $this->date_commentaire = $date; }
}
?>
