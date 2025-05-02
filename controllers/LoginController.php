<?php
// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,    // Only send cookies over HTTPS
        'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
        'use_strict_mode' => true   // Prevent session fixation
    ]);
}

// Include files with require_once to prevent multiple inclusions
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/User.php';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        // Validate CSRF token if you have one
        // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        //     throw new Exception('Invalid CSRF token');
        // }

        // Sanitize and validate input
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (empty($password)) {
            throw new Exception('Password cannot be empty');
        }

        // Check admin login
        $admin = Admin::findByEmail($email);
        if ($admin && password_verify($password, $admin['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Store minimal user data in session
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'name' => $admin['name'] ?? ''
            ];
            $_SESSION['role'] = 'admin';
            
            header('Location: ../views/dashboard.php');
            exit;
        }

        // Check user login
        $user = User::findByEmail($email);
        if ($user) {
            // Vérifier si l'utilisateur est bloqué
            if ($user['is_blocked']) {
                throw new Exception('Votre compte a été bloqué. Veuillez contacter un administrateur.');
            }

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'] ?? ''
                ];
                $_SESSION['role'] = 'user';
                
                header('Location: ../views/dashboard-admin.php');
                exit;
            }
        }

        // If we get here, authentication failed
        throw new Exception('Email ou mot de passe incorrect');
        
    } catch (Exception $e) {
        // Log the error (in a real app, use a proper logging system)
        error_log('Login error: ' . $e->getMessage());
        
        // Store error in session to display on redirect
        $_SESSION['login_error'] = $e->getMessage();
        header('Location: ../views/login.php');
        exit;
    }
}

// If not a POST request, redirect to login
header('Location: ../views/login.php');
exit;
?>