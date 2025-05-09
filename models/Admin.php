<?php
require_once __DIR__ . '/../config/Database.php';

class Admin {
    private $pdo;

    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? (new Database())->getConnection();
        // Ensure the admins table has timestamp columns
        $this->ensureTimestampColumns();
    }

    private function ensureTimestampColumns() {
        try {
            $this->pdo->query("
                ALTER TABLE admins 
                ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ");
        } catch (PDOException $e) {
            error_log("Error ensuring timestamp columns: " . $e->getMessage());
        }
    }

    public function login($email, $password) {
        $admin = $this->findByEmail($email);
        
        if (!$admin || !password_verify($password, $admin['password'])) {
            return false;
        }
        
        return $admin;
    }

    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT id, name, email, created_at FROM admins");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Admin getAll error: " . $e->getMessage());
            return [];
        }
    }

    public function create($name, $email, $password) {
        try {
            // Double-check if email exists in both tables
            $checkStmt = $this->pdo->prepare("
                SELECT 'admin' as type FROM admins WHERE email = ?
                UNION
                SELECT 'user' as type FROM users WHERE email = ?
            ");
            $checkStmt->execute([$email, $email]);
            
            if ($checkStmt->rowCount() > 0) {
                $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Email already exists as " . $result['type']);
                return false;
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO admins (name, email, password, created_at, updated_at) 
                VALUES (?, ?, ?, NOW(), NOW())
            ");
            
            return $stmt->execute([
                htmlspecialchars($name),
                htmlspecialchars($email),
                password_hash($password, PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            error_log("Admin create error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM admins WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Admin delete error: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Admin getById error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name, $email, $password = null) {
        try {
            // Check if email exists and belongs to a different admin
            $checkStmt = $this->pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $id]);
            if ($checkStmt->rowCount() > 0) {
                return false;
            }

            if ($password) {
                $stmt = $this->pdo->prepare("UPDATE admins SET name=?, email=?, password=?, updated_at=NOW() WHERE id=?");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                return $stmt->execute([htmlspecialchars($name), htmlspecialchars($email), $hashedPassword, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE admins SET name=?, email=?, updated_at=NOW() WHERE id=?");
                return $stmt->execute([htmlspecialchars($name), htmlspecialchars($email), $id]);
            }
        } catch (PDOException $e) {
            error_log("Admin update error: " . $e->getMessage());
            return false;
        }
    }

    public static function findByEmail($email) {
        try {
            $pdo = (new Database())->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Admin findByEmail error: " . $e->getMessage());
            return false;
        }
    }
}