<?php
session_start([
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true
]);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email invalide');
        }

        if (empty($password)) {
            throw new Exception('Le mot de passe ne peut pas être vide');
        }

        $db = new Database();
        $pdo = $db->getConnection();

        // Vérifier d'abord dans la table admins
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION = array();
            
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'name' => $admin['name'],
                'role' => 'admin'
            ];
            $_SESSION['role'] = 'admin';
            $_SESSION['isAdmin'] = true;
            
            header('Location: ../views/dashboard-admin.php');
            exit();
        }

        // Si ce n'est pas un admin, vérifier dans la table users
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['is_blocked']) {
                throw new Exception('Votre compte est bloqué. Contactez un administrateur.');
            }

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION = array();
                
                // Définir les variables de session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = 'user';
                
                header('Location: ../views/dashboard.php');
                exit();
            }
        }

        throw new Exception('Email ou mot de passe incorrect');
        
    } catch (Exception $e) {
        $_SESSION['login_error'] = $e->getMessage();
        header('Location: ../views/login.php');
        exit();
    }
}

header('Location: ../views/login.php');
exit();
?>