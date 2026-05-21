<?php
class Product {
    // Database connection and table name
    private $conn;
    private $table_name = "products";

    // Product properties
    public $id;
    public $sku;
    public $name;
    public $description;
    public $price;
    public $stock_level;
    public $image_url;
    public $created_at;
    public $updated_at;

    // Constructor to receive the database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Object method to create a new product in the database
    public function create() {
        // Check that the SKU is exactly 13 digits long and contains only numbers
        if (!preg_match('/^\d{13}$/', $this->sku)) {
            return false; // Rejects instantly if it's not exactly 13 numbers
        }
        // SQL query for PostgreSQL
        $query = "INSERT INTO " . $this->table_name . " 
                  (sku, name, description, price, stock_level, image_url) 
                  VALUES (:sku, :name, :description, :price, :stock_level, :image_url)";

        // Use the connection to prepare the query
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs to prevent XSS
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = filter_var($this->price, FILTER_VALIDATE_FLOAT);
        $this->stock_level = filter_var($this->stock_level, FILTER_VALIDATE_INT);
        $this->image_url = !empty($this->image_url) ? filter_var($this->image_url, FILTER_SANITIZE_URL) : null;

        // Bind values in the INSERT statement to the object properties
        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_level", $this->stock_level);
        $stmt->bindParam(":image_url", $this->image_url);

        // Execute the query and return true if successful
        try {
            if($stmt->execute()) {
                return true;
            }
        } catch (PDOException $exception) {
            // Catch PostgreSQL code 23505, a unique violation when email already exists
            if ($exception->getCode() == '23505') {
                return 'exists';
            }
            // Log other errors
            error_log("Database Error: " . $exception->getMessage());
            return false;
        }
        return false;
    }

    // View Product details including image reference
    public function readOne() {
        // SQL query to select a single product by ID
        $query = "SELECT id, sku, name, description, price, stock_level, image_url, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->sku = $row['sku'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->stock_level = $row['stock_level'];
            $this->image_url = $row['image_url'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Dynamic update to product details with input validation and sanitization
    public function update($data) {
        // Whitelist array prevents parameter pollution/mass-assignment attacks
        $allowedFields = ['sku', 'name', 'description', 'price', 'stock_level', 'image_url'];
        $fieldsToUpdate = [];
        $bindings = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fieldsToUpdate[] = "{$key} = :{$key}";
                if ($key === 'image_url') {
                    $bindings[":{$key}"] = !empty($value) ? filter_var($value, FILTER_SANITIZE_URL) : null;
                } else {
                    $bindings[":{$key}"] = htmlspecialchars(strip_tags($value));
                }
            }
        }

        if (empty($fieldsToUpdate)) {
            return false;
        }

        // Build the SQL query dynamically based on provided fields
        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($bindings as $placeholder => $val) {
            $stmt->bindValue($placeholder, $val);
        }
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete Product from table
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>
