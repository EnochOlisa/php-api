<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include necessary files using the established directory structure
require_once "../../config/session_config.php";
start_secure_session();

// Unset all session variables
session_unset();

// Destroy the session on the server
session_destroy();

// Delete the session cookie in the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

http_response_code(200);
echo json_encode(["message" => "Logout successful."]);
?>