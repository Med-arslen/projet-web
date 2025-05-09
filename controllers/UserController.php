<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $db;
    private $userModel;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        try {
            $this->db = new Database();
            $this->userModel = new User($this->db->getConnection());
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur de connexion à la base de données";
            header('Location: ../views/index.php');
            exit;
        }
    }

    public function handleRequest() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['create'])) {
                    $this->createUser();
                } elseif (isset($_POST['update'])) {
                    $this->updateUser();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
                    $this->updateProfile();
                }
            } elseif (isset($_GET['delete'])) {
                $this->deleteUser();
            }

            $this->displayUsers();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../views/index.php?view=user');
            exit;
        }
    }

    private function createUser() {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['admin_id'])) {
            throw new Exception("Tous les champs obligatoires doivent être remplis");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide");
        }

        $stmt = $this->db->getConnection()->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cet email est déjà utilisé");
        }

        $created = $this->userModel->create(
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
        header('Location: ../views/index.php?view=user');
        exit;
    }

    private function updateUser() {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['admin_id'])) {
            throw new Exception("Le nom, l'email et l'ID admin sont requis");
        }

        $updated = $this->userModel->update(
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
        header('Location: ../views/index.php?view=user');
        exit;
    }

    private function deleteUser() {
        $deleted = $this->userModel->delete($_GET['delete']);
        if (!$deleted) {
            throw new Exception("Erreur lors de la suppression de l'utilisateur");
        }
        $_SESSION['success'] = "Utilisateur supprimé avec succès";
        header('Location: ../views/index.php?view=user');
        exit;
    }

    private function displayUsers() {
        $users = $this->userModel->getAll();
        include __DIR__ . '/../views/user/list.php';
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
            $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
            $userId = $_SESSION['user']['id'];

            // Validation de base
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = "Le nom et l'email sont requis.";
                header('Location: ../views/user/edit-profile.php');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "L'email n'est pas valide.";
                header('Location: ../views/user/edit-profile.php');
                exit();
            }

            try {
                // Vérifier si l'email existe déjà pour un autre utilisateur
                $stmt = $this->db->getConnection()->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $userId]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['error'] = "Cet email est déjà utilisé.";
                    header('Location: ../views/user/edit-profile.php');
                    exit();
                }

                // Construction de la requête de mise à jour
                $updateData = [
                    'name' => $name,
                    'email' => $email
                ];
                
                $updateFields = ['name = :name', 'email = :email'];
                
                // Traitement du mot de passe si fourni
                if (!empty($newPassword)) {
                    if ($newPassword !== $confirmPassword) {
                        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                        header('Location: ../views/user/edit-profile.php');
                        exit();
                    }
                    if (strlen($newPassword) < 8) {
                        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
                        header('Location: ../views/user/edit-profile.php');
                        exit();
                    }
                    $updateFields[] = 'password = :password';
                    $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                
                // Construction et exécution de la requête finale
                $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $updateData['id'] = $userId;
                
                $stmt = $this->db->getConnection()->prepare($sql);
                $result = $stmt->execute($updateData);

                if (!$result) {
                    throw new PDOException("Échec de la mise à jour du profil");
                }

                // Mise à jour des données de session
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;

                $_SESSION['success'] = "Profil mis à jour avec succès !";
                header('Location: ../views/dashboard.php');
                exit();

            } catch (PDOException $e) {
                $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil.";
                header('Location: ../views/user/edit-profile.php');
                exit();
            }
        }
    }
}
