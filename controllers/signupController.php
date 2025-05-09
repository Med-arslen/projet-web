<?php
session_start();
require_once '../models/User.php';
require_once '../models/Admin.php';
require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;

    try {
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }

        // Database connection
        $db = new Database();
        $pdo = $db->getConnection();

        // Obtenir l'ID du premier administrateur disponible
        $stmt = $pdo->query("SELECT id FROM admins ORDER BY id LIMIT 1");
        $defaultAdminId = $stmt->fetchColumn();

        if (!$defaultAdminId) {
            throw new Exception("Erreur système : Aucun administrateur n'est disponible.");
        }

        if ($role === 'admin') {
            // Créer un nouvel administrateur
            $adminModel = new Admin($pdo);
            if (!$adminModel->create($name, $email, $password)) {
                throw new Exception("L'email est déjà utilisé ou une erreur s'est produite lors de la création du compte administrateur.");
            }
            $_SESSION['success'] = "Compte administrateur créé avec succès. Vous pouvez maintenant vous connecter.";
        } else {
            // Créer un nouvel utilisateur
            $userModel = new User($pdo);
            if (!$userModel->create($defaultAdminId, $name, $email, $password, 'user', $phone)) {
                throw new Exception("L'email est déjà utilisé ou une erreur s'est produite lors de la création du compte utilisateur.");
            }
            $_SESSION['success'] = "Compte utilisateur créé avec succès. Vous pouvez maintenant vous connecter.";
        }

        header("Location: ../views/login.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role
        ];
        header("Location: ../views/signup.php");
        exit();
    }
}

// Si ce n'est pas une requête POST, rediriger vers la page d'inscription
header("Location: ../views/signup.php");
exit();
