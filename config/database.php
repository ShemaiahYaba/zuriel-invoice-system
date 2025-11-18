<?php
/**
 * Database Configuration and Connection
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Database credentials
    private $host = 'localhost';
    private $dbname = 'zuriel_invoice_system';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Show detailed error in development
            die("<h1>Database Connection Failed</h1>" . 
                "<p><strong>Error:</strong> " . $e->getMessage() . "</p>" .
                "<p><strong>Check:</strong></p>" .
                "<ul>" .
                "<li>MySQL is running in XAMPP Control Panel</li>" .
                "<li>Database name: <code>{$this->dbname}</code> exists</li>" .
                "<li>Username: <code>{$this->username}</code></li>" .
                "<li>Password is correct</li>" .
                "</ul>");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>