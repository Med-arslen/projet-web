<?php
class Commande {
    private $id_commande;
    private $id_produit;
    private $nom_client;
    private $adresse;
    private $date_commande;
    private $quantite;
    private $produits = [];

    public function __construct($id_commande = null, $id_produit = null, $nom_client = null, $adresse = null, $date_commande = null, $quantite = null) {
        $this->id_commande = $id_commande;
        $this->id_produit = $id_produit;
        $this->nom_client = $nom_client;
        $this->adresse = $adresse;
        $this->date_commande = $date_commande;
        $this->quantite = $quantite;
    }

    // --- GETTERS ---
    public function getIdCommande() { return $this->id_commande; }
    public function getIdProduit() { return $this->id_produit; }
    public function getNomClient() { return $this->nom_client; }
    public function getAdresse() { return $this->adresse; }
    public function getDateCommande() { return $this->date_commande; }
    public function getQuantite() { return $this->quantite; }
    public function getProduits() { return $this->produits; }

    // --- SETTERS ---
    public function setIdCommande($id) { $this->id_commande = $id; }
    public function setIdProduit($id) { $this->id_produit = $id; }
    public function setNomClient($nom) { $this->nom_client = $nom; }
    public function setAdresse($adresse) { $this->adresse = $adresse; }
    public function setDateCommande($date) { $this->date_commande = $date; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }
    public function setProduits($produits) {
        $this->produits = $produits;
    }
}
?>
