<?php
class Produit {
    private $id_produit;
    private $nom;
    private $description;
    private $prix;
    private $quantite;
    private $image;

    public function __construct($id_produit = null, $nom = null, $description = null, $prix = null, $quantite = null, $image = null) {
        $this->id_produit = $id_produit;
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->quantite = $quantite;
        $this->image = $image;
    }

    // --- GETTERS ---
    public function getId() { return $this->id_produit; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getPrix() { return $this->prix; }
    public function getQuantite() { return $this->quantite; }
    public function getImage() { return $this->image; }

    // --- SETTERS ---
    public function setId($id) { $this->id_produit = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }
    public function setImage($image) { $this->image = $image; }
}
?>
