<?php
include 'C:\xampp\htdocs\projetweb\Model\Produit.php';
include 'C:\xampp\htdocs\projetweb\config.php';

class ProduitController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create
    public function createProduit($nom, $description, $prix, $quantite, $image) {
        // Générer un ID aléatoire unique
        $id_produit = uniqid();

        $stmt = $this->pdo->prepare("INSERT INTO produit (id_produit, nom, description, prix, quantite, image) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_produit, $nom, $description, $prix, $quantite, $image]);
    }

    // Read all
    public function getAllProduits() {
        $stmt = $this->pdo->query("SELECT * FROM produit");
        $produits = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produits[] = new Produit($row['id_produit'], $row['nom'], $row['description'], $row['prix'], $row['quantite'], $row['image']);
        }
        return $produits;
    }

    // Read one
    public function getProduitById($id_produit) {
        $stmt = $this->pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
        $stmt->execute([$id_produit]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Produit($row['id_produit'], $row['nom'], $row['description'], $row['prix'], $row['quantite'], $row['image']);
        }
        return null;
    }

    // Update
    public function updateProduit($id_produit, $nom, $description, $prix, $quantite, $image) {
        $stmt = $this->pdo->prepare("UPDATE produit SET nom = ?, description = ?, prix = ?, quantite = ?, image = ? WHERE id_produit = ?");
        return $stmt->execute([$nom, $description, $prix, $quantite, $image, $id_produit]);
    }

    // Delete
    public function deleteProduit($id_produit) {
        try {
            // Supprimer uniquement le produit
            $stmt = $this->pdo->prepare("DELETE FROM produit WHERE id_produit = ?");
            $stmt->execute([$id_produit]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
