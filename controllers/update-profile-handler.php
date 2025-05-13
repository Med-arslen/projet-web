<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'updateProfile') {
    header('Location: ../views/dashboard.php');
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "Vous devez être connecté pour modifier votre profil";
    header('Location: ../views/login.php');
    exit();
}

// Vérifier que tous les champs requis sont présents
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['current_password'])) {
    $_SESSION['error'] = "Le nom, l'email et le mot de passe actuel sont requis";
    header('Location: ../views/user/edit-profile.php');
    exit();
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Vérifier le mot de passe actuel
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($_POST['current_password'], $user['password'])) {
        $_SESSION['error'] = "Le mot de passe actuel est incorrect";
        header('Location: ../views/user/edit-profile.php');
        exit();
    }

    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$_POST['email'], $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Cet email est déjà utilisé par un autre utilisateur";
        header('Location: ../views/user/edit-profile.php');
        exit();
    }

    // Préparer les données à mettre à jour
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'id' => $_SESSION['user_id']
    ];

    // Si un nouveau mot de passe est fourni
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 8) {
            $_SESSION['error'] = "Le nouveau mot de passe doit faire au moins 8 caractères";
            header('Location: ../views/user/edit-profile.php');
            exit();
        }
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas";
            header('Location: ../views/user/edit-profile.php');
            exit();
        }
        $data['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }

    // Mettre à jour le profil
    $sql = "UPDATE users SET name = :name, email = :email" . 
           (isset($data['password']) ? ", password = :password" : "") . 
           " WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    // Mettre à jour les données de session
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['email'] = $_POST['email'];

    $_SESSION['success'] = "Votre profil a été mis à jour avec succès";
    header('Location: ../views/dashboard.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil";
    header('Location: ../views/user/edit-profile.php');
    exit();
}