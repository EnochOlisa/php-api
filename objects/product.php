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

        // Sanitize string inputs to prevent XSS
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Validate and enforce correct strict numeric/data types
        $validatedPrice = filter_var($this->price, FILTER_VALIDATE_FLOAT);
        $validatedStock = filter_var($this->stock_level, FILTER_VALIDATE_INT);
        $this->image_url = !empty($this->image_url) ? filter_var($this->image_url, FILTER_SANITIZE_URL) : null;

        // Abort processing safely if critical metrics fail structure checks
        if ($validatedPrice === false || $validatedStock === false) {
            return false;
        }

        $this->price = $validatedPrice;
        $this->stock_level = $validatedStock;

        // SQL query for PostgreSQL
        $query = "INSERT INTO " . $this->table_name . " 
                  (sku, name, description, price, stock_level, image_url) 
                  VALUES (:sku, :name, :description, :price, :stock_level, :image_url)";

        // Use the connection to prepare the query
        $stmt = $this->conn->prepare($query);

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
            // Catch PostgreSQL code 23505 (Unique Key Constraint Violation)
            if ($exception->getCode() == '23505') {
                return 'exists';
            }
            // Log other system background errors anonymously
            error_log("Database Error inside create(): " . $exception->getMessage());
            return false;
        }
        return false;
    }

    // View product details by ID including image reference
    public function readOneByID() {
        // SQL query to select a single product by ID
        $query = "SELECT id, sku, name, description, price, stock_level, image_url, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);

        // Sanitize and validate that the ID is strictly an integer context
        $this->id = filter_var($this->id, FILTER_VALIDATE_INT);

        // If the input is not a valid integer, reject execution immediately
        if ($this->id === false) {
            return false;
        }

        // Bind parameter securely using explicit integer type casting mapping
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

    // View Product details by SKU including image reference
    public function readOneBySku() {
        // Parameterized search query matching the unique business SKU index
        $query = "SELECT id, sku, name, description, price, stock_level, image_url, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE sku = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);

        // Sanitize string to eliminate threat factors before binding
        $this->sku = htmlspecialchars(strip_tags($this->sku));
        $stmt->bindParam(1, $this->sku, PDO::PARAM_STR);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Hydrate the object properties with findings
            $this->id = $row['id'];
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

        // Cast object payload elements if parsed from raw client JSON input
        $dataArray = (array)$data;

        foreach ($dataArray as $key => $value) {
            if (in_array($key, $allowedFields)) {

                // Extra guard: If client is patching the barcode SKU, apply pattern rules
                if ($key === 'sku' && !preg_match('/^\d{13}$/', $value)) {
                    return false;
                }

                if ($key === 'image_url') {
                    $bindings[":{$key}"] = !empty($value) ? filter_var($value, FILTER_SANITIZE_URL) : null;
                } elseif ($key === 'price') {
                    $bindings[":{$key}"] = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($bindings[":{$key}"] === false) return false;
                } elseif ($key === 'stock_level') {
                    $bindings[":{$key}"] = filter_var($value, FILTER_VALIDATE_INT);
                    if ($bindings[":{$key}"] === false) return false;
                } else {
                    $bindings[":{$key}"] = htmlspecialchars(strip_tags($value));
                }

                $fieldsToUpdate[] = "{$key} = :{$key}";
            }
        }

        if (empty($fieldsToUpdate)) {
            return false;
        }

        // Build the SQL query dynamically based on provided fields safely
        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            foreach ($bindings as $placeholder => $val) {
                $stmt->bindValue($placeholder, $val);
            }

            $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
            if ($this->id === false) return false;

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Database Error inside update(): " . $e->getMessage());
            return false;
        }
        return false;
    }

    // Delete Product from table
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
        if ($this->id === false) {
            return false;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

        try {
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Database Error inside delete(): " . $e->getMessage());
            return false;
        }
        return false;
    }
}
?>