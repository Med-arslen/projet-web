<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../config/Database.php';

class AdminController {
    private $pdo;
    private $adminModel;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->adminModel = new Admin($this->pdo);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create'])) {
                $this->adminModel->create($_POST['name'], $_POST['email'], $_POST['password']);
            } elseif (isset($_POST['update'])) {
                $this->adminModel->update($_POST['id'], $_POST['name'], $_POST['email'], $_POST['password']);
            } elseif (isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
                $this->updateProfile();
            }
            header('Location: index.php?view=admin');
            exit;
        }

        if (isset($_GET['delete'])) {
            $this->adminModel->delete($_GET['delete']);
            header('Location: index.php?view=admin');
            exit;
        }

        $admins = $this->adminModel->getAll();
        include __DIR__ . '/../views/admin/list.php';
    }

    public function updateProfile() {
        session_start();
        if (!isset($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre profil.";
            header('Location: ../views/login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
            $userId = $_SESSION['user']['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $currentPassword = isset($_POST['current_password']) ? trim($_POST['current_password']) : '';
            $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
            $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

            // Debug log
            error_log("Debug - User ID: " . $userId);
            error_log("Debug - Current Password provided: " . ($currentPassword ? "Yes" : "No"));

            // Validation des données
            if (empty($name) || strlen($name) < 2) {
                $_SESSION['error'] = "Le nom doit contenir au moins 2 caractères.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "L'email n'est pas valide.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            // Vérifier le mot de passe actuel dans la table admins
            $stmt = $this->pdo->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$userId]);
            $admin = $stmt->fetch();

            // Debug log
            error_log("Debug - Admin found in DB: " . ($admin ? "Yes" : "No"));
            if ($admin) {
                error_log("Debug - Stored password hash: " . $admin['password']);
                error_log("Debug - Password verify result: " . (password_verify($currentPassword, $admin['password']) ? "True" : "False"));
            }

            if (empty($currentPassword)) {
                $_SESSION['error'] = "Le mot de passe actuel est requis pour toute modification.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            if (!$admin || !password_verify($currentPassword, $admin['password'])) {
                $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            // Vérification si l'email existe déjà pour un autre administrateur
            $stmt = $this->pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Cet email est déjà utilisé par un autre administrateur.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            // Si un nouveau mot de passe est fourni
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $_SESSION['error'] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                    header('Location: ../views/admin/edit-profile.php');
                    exit();
                }

                if ($newPassword !== $confirmPassword) {
                    $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                    header('Location: ../views/admin/edit-profile.php');
                    exit();
                }

                // Mise à jour avec nouveau mot de passe
                try {
                    $stmt = $this->pdo->prepare("
                        UPDATE admins 
                        SET name = ?, email = ?, password = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt->execute([
                        $name,
                        $email,
                        $hashedPassword,
                        $userId
                    ]);
                    // Debug log
                    error_log("Debug - Password update successful");
                } catch (PDOException $e) {
                    error_log("Debug - Password update error: " . $e->getMessage());
                    $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
                    header('Location: ../views/admin/edit-profile.php');
                    exit();
                }
            } else {
                // Mise à jour sans mot de passe
                try {
                    $stmt = $this->pdo->prepare("
                        UPDATE admins 
                        SET name = ?, email = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $email, $userId]);
                    // Debug log
                    error_log("Debug - Profile update successful (no password change)");
                } catch (PDOException $e) {
                    error_log("Debug - Profile update error: " . $e->getMessage());
                    $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
                    header('Location: ../views/admin/edit-profile.php');
                    exit();
                }
            }

            // Mise à jour réussie - mettre à jour les données de session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header('Location: ../views/admin/edit-profile.php');
            exit();
        }
    }
}

// Instancier et exécuter le contrôleur
$controller = new AdminController();
$controller->handleRequest();
