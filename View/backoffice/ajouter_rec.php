<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';
require_once __DIR__ . '/../../Model/Reclamation.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomprenom = $_POST['nomprenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $nomfilm = $_POST['nomfilm'] ?? '';
    $type_rec = $_POST['type_rec'] ?? '';
    $detail = $_POST['detail'] ?? '';
    $reponse_rec = '';

    // Vérifications de base
    if (empty($nomprenom) || empty($email) || empty($nomfilm) || empty($type_rec) || empty($detail)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        exit;
    }

    $alphaRegex = '/^[A-Za-zÀ-ÖØ-öø-ÿ\s\'-]+$/';
    if (!preg_match($alphaRegex, $nomprenom)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nom et prénom non valides']);
        exit;
    }

    // Création de l'objet réclamation
    $reclamationn = new Reclamation($nomprenom, $email, $nomfilm, $type_rec, $detail, $reponse_rec, null);
    $ReclamationController = new ReclamationController();

    // Ajout de la réclamation
    $id_rec = $ReclamationController->addReclamation($reclamationn);
    if ($id_rec) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Réclamation enregistrée', 'id_rec' => $id_rec]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
exit;
?>
