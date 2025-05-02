<?php
require_once 'config/Database.php';
require_once 'models/User.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $db = new Database();
        $pdo = $db->getConnection();
        $userModel = new User($pdo);

        $userId = (int)$_POST['id'];
        
        // Vérifier si l'utilisateur existe
        $user = $userModel->getById($userId);
        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ]);
            exit;
        }

        // Supprimer l'utilisateur
        $success = $userModel->delete($userId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Requête invalide'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
}