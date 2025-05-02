<?php
require_once 'models/User.php';
require_once 'config/Database.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $userModel = new User($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['create'])) {
            // Valider les données
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['admin_id'])) {
                throw new Exception("Tous les champs obligatoires doivent être remplis");
            }

            // Valider l'email
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format d'email invalide");
            }

            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cet email est déjà utilisé");
            }

            $created = $userModel->create(
                $_POST['admin_id'],
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role'] ?? 'user',
                $_POST['phone'] ?? null
            );

            if (!$created) {
                throw new Exception("Erreur lors de la création de l'utilisateur");
            }

            $_SESSION['success'] = "Utilisateur créé avec succès";
            
        } elseif (isset($_POST['update'])) {
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['admin_id'])) {
                throw new Exception("Le nom, l'email et l'ID admin sont requis");
            }

            $updated = $userModel->update(
                $_POST['id'],
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role'] ?? 'user',
                $_POST['phone'] ?? null
            );

            if (!$updated) {
                throw new Exception("Erreur lors de la mise à jour de l'utilisateur");
            }

            $_SESSION['success'] = "Utilisateur mis à jour avec succès";
        }
        
        header('Location: index.php?view=user');
        exit;
    }

    if (isset($_GET['delete'])) {
        $deleted = $userModel->delete($_GET['delete']);
        if (!$deleted) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur");
        }
        $_SESSION['success'] = "Utilisateur supprimé avec succès";
        header('Location: index.php?view=user');
        exit;
    }

    $users = $userModel->getAll();
    include 'views/user/list.php';

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: index.php?view=user');
    exit;
}
