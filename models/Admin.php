<?php
require_once __DIR__ . '/../config/Database.php';

class Admin {
    private $pdo;

    public function __construct($pdo = null) {
        $this->pdo = $pdo ?? (new Database())->getConnection();
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
            $stmt = $this->pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            return $stmt->execute([$name, $email, $hashedPassword]);
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
            if ($password) {
                $stmt = $this->pdo->prepare("UPDATE admins SET name=?, email=?, password=? WHERE id=?");
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                return $stmt->execute([$name, $email, $hashedPassword, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE admins SET name=?, email=? WHERE id=?");
                return $stmt->execute([$name, $email, $id]);
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