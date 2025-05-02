<?php
require_once '../config/Database.php'; 

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Get all users
    public function getAll() {
        return $this->pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new user
    public function create($admin_id, $name, $email, $password, $role = 'user', $phone = null) {
        $stmt = $this->pdo->prepare("INSERT INTO users (admin_id, name, email, password, role, phone) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $admin_id, 
            $name, 
            $email, 
            password_hash($password, PASSWORD_DEFAULT), 
            $role, 
            $phone
        ]);
    }

    // Delete a user by ID
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get user by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user details
    public function update($id, $name, $email, $password, $role, $phone) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, phone = ? WHERE id = ?");
        return $stmt->execute([
            $name, 
            $email, 
            password_hash($password, PASSWORD_DEFAULT), 
            $role, 
            $phone, 
            $id
        ]);
    }

    public function blockUser($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_blocked = TRUE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function unblockUser($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_blocked = FALSE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function isBlocked($id) {
        $stmt = $this->pdo->prepare("SELECT is_blocked FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    // Find user by email
    public static function findByEmail($email) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Static method to create user
    public static function createStatic($admin_id, $name, $email, $password, $role = 'user', $phone = null) {
        $db = Database::getConnection();

        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $password = password_hash($password, PASSWORD_BCRYPT);
        $role = htmlspecialchars($role);
        $phone = $phone ? htmlspecialchars($phone) : null;

        $sql = "INSERT INTO users (admin_id, name, email, password, role, phone) 
                VALUES (:admin_id, :name, :email, :password, :role, :phone)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':phone', $phone);

        return $stmt->execute();
    }

    // Static method to find user by email
    public static function findByEmailStatic($email) {
        $db = Database::getConnection();
        $email = htmlspecialchars($email);

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generateQRCode($userId) {
        require_once __DIR__ . '/../services/QRCodeService.php';
        $qrService = new QRCodeService();
        
        // Récupérer les informations de l'utilisateur
        $user = $this->getById($userId);
        if (!$user) {
            return false;
        }

        // Données à encoder dans le QR code
        $qrData = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Générer le QR code
        return $qrService->generateQRCode($userId, $qrData);
    }

    public function getQRCodePath($userId) {
        return '/assets/qrcodes/qr_' . $userId . '.png';
    }
}
?>
