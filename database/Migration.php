<?php
class Migration {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn = null;

    public function __construct() {
        $config = require_once __DIR__ . '/../config/database.php';

        $this->host = $config['host'];
        $this->db_name = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        try {
            // First, connect without database to create it if it doesn't exist
            $this->conn = new PDO("mysql:host=$this->host", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create database if it doesn't exist
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS $this->db_name");

            // Connect to the specific database
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function migrate() {
        try {
            // Users table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Events table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                user_id INT NOT NULL,
                max_capacity INT NOT NULL,
                event_datetime DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )");

            // Event attendees table
            $this->conn->exec("CREATE TABLE IF NOT EXISTS event_attendees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                attendee_name VARCHAR(100) NOT NULL,
                attendee_email VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (event_id) REFERENCES events(id)
            )");

            echo "Migration completed successfully!\n";
        } catch(PDOException $e) {
            die("Migration failed: " . $e->getMessage());
        }
    }

    public function seed() {
        try {
            // Clear existing data
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");
            $this->conn->exec("TRUNCATE TABLE event_attendees");
            $this->conn->exec("TRUNCATE TABLE events");
            $this->conn->exec("TRUNCATE TABLE users");
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");

            // Insert admin user
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Admin User', 'admin@example.com', $adminPassword, 'admin']);

            // Insert regular user
            $userPassword = password_hash('user123', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Regular User', 'user@example.com', $userPassword, 'user']);

            echo "Seeding completed successfully!\n";
        } catch(PDOException $e) {
            die("Seeding failed: " . $e->getMessage());
        }
    }
}