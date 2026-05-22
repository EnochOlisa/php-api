<?php
class Cart {
    private $conn;
    private $table_name = "carts";

    // Object properties
    public $id;
    public $user_id;
    public $session_id;
    public $items = [];

    // Dependency injection of the database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Resolves or creates an active cart for a user/session
    public function getOrCreateCart($userId, $sessionId = null) {
        // FIX: Changed MySQL 'LIMIT 0,1' syntax to standard ANSI/PostgreSQL 'LIMIT 1'
        $query = "SELECT id FROM " . $this->table_name . " 
              WHERE user_id = :user_id OR (user_id IS NULL AND session_id = :session_id) 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":session_id", $sessionId);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id = $row['id'];
        } else {
            $this->createCart($userId, $sessionId);
        }
        return $this->id;
    }

    private function createCart($userId, $sessionId) {
        // FIX: Re-written from MySQL 'SET' syntax to Standard ANSI 'VALUES' syntax for PostgreSQL
        $query = "INSERT INTO " . $this->table_name . " (user_id, session_id) 
              VALUES (:user_id, :session_id)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters cleanly using safe prepared statements
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":session_id", $sessionId);
        $stmt->execute();

        // Dynamically fetch the auto-generated primary SERIAL sequence key
        $this->id = $this->conn->lastInsertId();
    }
}
?>