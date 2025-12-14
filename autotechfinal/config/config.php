<?php

class Config {
    private static $pdo = NULL;
    
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'autotech_db';
    private const DB_USER = 'root';
    private const DB_PASS = 'azerty1234*';
    
    /**
     * Obtenir la connexion PDO à la base de données
     * @return PDO
     */
    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
                    self::DB_USER,
                    self::DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch(PDOException $e) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Tunis');

define('BASE_URL', '/AutoTech_Integrated/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
?>
