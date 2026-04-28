<?php
class Database {
    // PostgreSQL connection details
    private $host = "localhost";
    private $db_name = "php-api";
    private $username = "postgres";
    private $password = "Standard@123";
    private $port = "5432"; // Default PostgreSQL port
    public $conn;

    // Method to get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Using the 'pgsql' driver for PostgreSQL
            $this->conn = new PDO(
                "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Set error mode to exception for better error handling
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            // Log the actual detailed error for the developer to see in the background
            error_log("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>