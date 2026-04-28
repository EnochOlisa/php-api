<?php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";

    // User properties
    public $id;
    public $first_name;
    public $last_name;
    public $phone;
    public $address_line1;
    public $city;
    public $province; // Canada style: e.g., ON, BC, QC
    public $postal_code; // Canada style: e.g., K1A 0B1
    public $email;
    public $password;
    public $registration_date;
    public $date_of_birth;

    // Constructor to receive the database connection when in
    public function __construct($db) {
        $this->conn = $db;
    }

    // Object method to create a new user in the database
    public function create() {
        // SQL query for PostgreSQL
        $query = "INSERT INTO " . $this->table_name . " (
            first_name, 
            last_name, 
            phone, 
            address_line1, 
            city, 
            province, 
            postal_code, 
            email, 
            password, 
            date_of_birth, 
            registration_date
          ) VALUES (
            :first_name, 
            :last_name, 
            :phone, 
            :address_line1, 
            :city, 
            :province, 
            :postal_code, 
            :email, 
            :password, 
            :date_of_birth, 
            :registration_date
          )";

        // Use the connection to prepare the query
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs to prevent XSS
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address_line1 = htmlspecialchars(strip_tags($this->address_line1));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->province = htmlspecialchars(strip_tags($this->province));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));
        $this->registration_date = htmlspecialchars(strip_tags($this->registration_date));

        // Bind values in the INSERT statement to the object properties
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address_line1", $this->address_line1);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":province", $this->province);
        $stmt->bindParam(":postal_code", $this->postal_code);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password); // Already hashed in create.php
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);
        $stmt->bindParam(":registration_date", $this->registration_date);

        // Execute the query
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

    // Check if email exists to verify user existence
    public function emailExists() {
        // Corrected PostgreSQL query
        $query = "SELECT id, first_name, last_name, password 
              FROM " . $this->table_name . " 
              WHERE email = ? 
              LIMIT 1"; // Simpler and valid for PostgreSQL

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            // ... (rest of your logic)
            return true;
        }
        return false;
    }

// Method to update user data (excluding immutable fields)
    public function update($fields) {
        // 1. Define allowed updateable fields (Immutable fields like email/id are excluded)
        $allowedFields = [
            'first_name', 'last_name', 'phone', 'address_line1',
            'city', 'province', 'postal_code', 'date_of_birth'
        ];

        $updateParts = [];
        $params = [];

        // 2. Loop through the JSON data and build the query dynamically
        foreach ($fields as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateParts[] = "{$key} = :{$key}";
                $params[":{$key}"] = htmlspecialchars(strip_tags($value));
            }
        }

        // If no valid fields were provided, stop here
        if (empty($updateParts)) {
            return false;
        }

        // 3. Build the final SQL statement
        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $updateParts) . " WHERE email = :email";

        $stmt = $this->conn->prepare($sql);

        // 4. Bind the identification email and the dynamic parameters
        $stmt->bindValue(':email', $this->email);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>