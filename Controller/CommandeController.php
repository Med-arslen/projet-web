<?php
include 'C:\xampp\htdocs\projetweb\Model\Commande.php';
include 'C:\xampp\htdocs\projetweb\config.php';

class CommandeController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create or merge
    public function createCommande($id_produit, $nom_client, $adresse, $quantite) {
        // Vérifier si une commande existe déjà pour le même client et produit dans l'heure actuelle
        $stmt = $this->pdo->prepare(
            "SELECT * FROM commande WHERE id_produit = ? AND nom_client = ? AND adresse = ? AND date_commande >= NOW() - INTERVAL 1 HOUR"
        );
        $stmt->execute([$id_produit, $nom_client, $adresse]);
        $existingCommande = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCommande) {
            // Mettre à jour la quantité de la commande existante
            $newQuantite = $existingCommande['quantite'] + $quantite;
            $updateStmt = $this->pdo->prepare(
                "UPDATE commande SET quantite = ? WHERE id_commande = ?"
            );
            return $updateStmt->execute([$newQuantite, $existingCommande['id_commande']]);
        } else {
            // Créer une nouvelle commande
            $stmt = $this->pdo->prepare(
                "INSERT INTO commande (id_produit, nom_client, adresse, quantite, date_commande) VALUES (?, ?, ?, ?, NOW())"
            );
            return $stmt->execute([$id_produit, $nom_client, $adresse, $quantite]);
        }
    }

    // Read all
    public function getAllCommandes() {
        $stmt = $this->pdo->query("SELECT * FROM commande");
        $commandes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = $row['id_produit'] . '|' . $row['nom_client'] . '|' . $row['adresse'];

            if (!isset($commandes[$key])) {
                $commandes[$key] = [
                    'id' => $row['id_commande'],
                    'quantite' => $row['quantite'],
                    'id_produit' => $row['id_produit'],
                    'nom_client' => $row['nom_client'],
                    'adresse' => $row['adresse'],
                    'date_commande' => $row['date_commande']
                ];
            } else {
                $commandes[$key]['id'] .= '/' . $row['id_commande'];
                $commandes[$key]['quantite'] .= '/' . $row['quantite'];
            }
        }

        return array_values($commandes);
    }

    // Read by ID
    public function getCommandeById($id_commande) {
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Commande($row['id_commande'], $row['id_produit'], $row['nom_client'], $row['adresse'], $row['date_commande'], $row['quantite']);
        }
        return null;
    }

    // Update
    public function updateCommande($id_commande, $id_produit, $nom_client, $adresse, $quantite) {
        $stmt = $this->pdo->prepare("UPDATE commande SET id_produit = ?, nom_client = ?, adresse = ?, quantite = ? WHERE id_commande = ?");
        return $stmt->execute([$id_produit, $nom_client, $adresse, $quantite, $id_commande]);
    }

    // Delete
    public function deleteCommande($id_commande) {
        $stmt = $this->pdo->prepare("DELETE FROM commande WHERE id_commande = ?");
        return $stmt->execute([$id_commande]);
    }

    // Read by Product ID
    public function getCommandesByProduit($id_produit) {
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE id_produit = ?");
        $stmt->execute([$id_produit]);
        $commandes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commandes[] = new Commande($row['id_commande'], $row['id_produit'], $row['nom_client'], $row['adresse'], $row['date_commande'], $row['quantite']);
        }
        return $commandes;
    }
}
?>
