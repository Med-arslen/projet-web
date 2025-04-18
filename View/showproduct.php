<?php
include_once '../Controller/ProduitController.php';

if (isset($_GET['id'])) {
    $controller = new ProduitController($pdo);
    $produit = $controller->getProduitById($_GET['id']);
    
    if ($produit) {
        $response = [
            'success' => true,
            'produit' => [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'prix' => $produit->getPrix(),
                'image' => $produit->getImage(),
                'quantite' => $produit->getQuantite()
            ]
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Produit non trouvé'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'ID du produit non fourni'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);