<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Include necessary files using the established directory structure
require_once "../../config/database.php";
require_once "../../config/session_config.php";
require_once "../../objects/user.php";

// Include session configuration and start a secure session
start_secure_session();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(["authenticated" => false, "message" => "Not logged in."]);
    exit;
}

// Connect to DB to get fresh details
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Use the email from the SESSION as the source of truth
$user->email = $_SESSION['email'];

if ($user->emailExists()) {
    // We now have all details loaded into the $user object properties
    http_response_code(200);
    echo json_encode([
        "authenticated" => true,
        "user" => [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "phone" => $user->phone,
            "address_line1" => $user->address_line1,
            "city" => $user->city,
            "province" => $user->province,
            "postal_code" => $user->postal_code,
            "date_of_birth" => $user->date_of_birth
        ]
    ]);
} else {
    // If the session exists but the user was deleted from the DB
    session_destroy();
    http_response_code(404);
    echo json_encode(["message" => "User account no longer exists."]);
}
?>