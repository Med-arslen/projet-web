<?php
// Prevent redeclaration
if (!class_exists('Database')) {
    
class Database {
    private static $dbHost = 'localhost';
    private static $dbName = 'films';
    private static $dbUser = 'root';
    private static $dbPass = '';

    public static function getConnection() {
        try {
            $pdo = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }
    }
}

} // <-- Fin du if class_exists
?>
