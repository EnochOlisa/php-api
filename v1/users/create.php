<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include necessary files using the established directory structure
include_once '../../config/database.php';
include_once '../../objects/user.php';

// Initialize database and User object
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get the posted raw data (JSON format)
$data = json_decode(file_get_contents("php://input"));

// Validation: Check for required fields
if (
    // Check if they are set and not empty (basic validation)
    !empty($data->first_name) &&
    !empty($data->last_name) &&
    !empty($data->email) &&
    !empty($data->password)
) {
    // Populate the User object properties
    $user->first_name = $data->first_name;
    $user->last_name = $data->last_name;
    $user->phone = $data->phone ?? null;
    $user->address_line1 = $data->address_line1 ?? null;
    $user->city = $data->city ?? null;
    $user->province = $data->province ?? null;
    $user->postal_code = $data->postal_code ?? null;
    $user->email = $data->email;
    $user->date_of_birth = $data->date_of_birth ?? null;

    // Set automated field
    $user->registration_date = date('Y-m-d H:i:s');

    // Secure Password Hashing: Argon2id
    // The password must be hashed here before being assigned to the object
    $user->password = password_hash($data->password, PASSWORD_ARGON2ID);

    // Attempt to create the user
    $result = $user->create();

    if ($result === true) {
        http_response_code(201);
        echo json_encode(["message" => "User was created successfully."]);

    } elseif ($result === 'exists') {
        http_response_code(409);
        echo json_encode(["message" => "Unable to create user. Email already exists."]);

    } else {
        http_response_code(503);
        echo json_encode(["message" => "Unable to create user. Service is currently unavailable."]);
    }
}
?>