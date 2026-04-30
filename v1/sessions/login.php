<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include necessary files using the established directory structure
require_once "../../config/database.php";
require_once "../../config/session_config.php";
require_once "../../objects/user.php";

// Initialize database and User object
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $user->email = $data->email;

    // Check if email exists and fetch user data
    if ($user->emailExists() && password_verify($data->password, $user->password)) {

        // Configuration applied, now start & secure
        start_secure_session();
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['user_id'] = $user->id;
        $_SESSION['email'] = $user->email;

        http_response_code(200);
        echo json_encode([
            "message" => "Login successful.",
            "user" => [
                "id" => $user->id,
                "email" => $user->email
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Login failed. Invalid email or password."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data. Email and password required."]);
}
?>