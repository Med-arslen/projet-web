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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
            $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

            // Validation basique
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = "Le nom et l'email sont requis.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            if (!empty($newPassword) && $newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }

            try {
                $updateData = [];
                $updateFields = [];
                
                // Construire la requête de mise à jour
                $updateFields[] = "name = ?";
                $updateData[] = $name;
                
                $updateFields[] = "email = ?";
                $updateData[] = $email;
                
                if (!empty($newPassword)) {
                    $updateFields[] = "password = ?";
                    $updateData[] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                
                $updateData[] = $_SESSION['user']['id']; // ID de l'admin pour la clause WHERE
                
                $sql = "UPDATE users SET " . implode(", ", $updateFields) . ", updated_at = NOW() WHERE id = ?";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute($updateData);

                if (!$result) {
                    throw new PDOException("Échec de la mise à jour du profil");
                }

                // Mise à jour des données de session
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;

                $_SESSION['success'] = "Profil mis à jour avec succès !";
                header('Location: ../views/dashboard-admin.php');
                exit();

            } catch (PDOException $e) {
                $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil.";
                header('Location: ../views/admin/edit-profile.php');
                exit();
            }
        }
    }
}

// Instancier et exécuter le contrôleur
$controller = new AdminController();
$controller->handleRequest();
