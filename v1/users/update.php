<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

// Include necessary files using the established directory structure
include_once '../../config/database.php';
include_once '../../objects/user.php';
include_once '../../config/session_config.php';

// Include session configuration and start a secure session
start_secure_session();

// Check if the user is authenticated before allowing profile updates
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Access denied. Please log in to update your profile."]);
    exit;
}

// Initialize database and User object
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get the posted raw data (JSON format)
$data = json_decode(file_get_contents("php://input"));

// Ensure the identifier (email) is provided
if(!empty($data->email)) {
    $user->email = $data->email;

    // Confirm user exists
    if($user->emailExists()) {

        // Perform the dynamic update using only the provided fields
        if($user->update($data)) {
            http_response_code(200);
            echo json_encode(["message" => "User updated successfully."]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No valid updateable fields were provided."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User does not exist."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "User email is required for update."]);
}
?>