<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

// 1. Ensure the identifier (email) is provided
if(!empty($data->email)) {
    $user->email = $data->email;

    // 2. Confirm user exists
    if($user->emailExists()) {

        // 3. Perform the dynamic update using only the provided fields
        if($user->update($data)) {
            http_response_code(200);
            echo json_encode(["message" => "User updated successfully."]);
        } else {
            http_response_code(400); // Bad Request if no valid fields provided
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