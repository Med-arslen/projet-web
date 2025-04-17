<?php
include '../config.php';
include '../Controller/ProduitController.php';

if (isset($_GET['id'])) {
    $id_produit = $_GET['id'];
    $controller = new ProduitController($pdo);

    try {
        $controller->deleteProduit($id_produit);
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        echo "Erreur lors de la suppression du produit : " . $e->getMessage();
    }
} else {
    echo "ID du produit non spécifié.";
}