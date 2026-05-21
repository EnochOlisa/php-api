<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include necessary files using the established directory structure
include_once '../../config/database.php';
include_once '../../objects/product.php';


// Initialize database and Product object, wrapped in a try-catch for error handling

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->sku) && !empty($data->name) && isset($data->price)) {
    $product->sku = $data->sku;
    $product->name = $data->name;
    $product->description = $data->description ?? null;
    $product->price = $data->price;
    $product->stock_level = $data->stock_level ?? 0;
    $product->image_url = $data->image_url ?? null; // Capture incoming static URL path string

    //Attempt to create the product
    $result = $product->create();

    if ($result === true) {
        http_response_code(201);
        echo json_encode(["message" => "Product created successfully."]);
    } elseif($result === 'exists') {
        http_response_code(409);
        echo json_encode(["message" => "Unable to create product. SKU already exists."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Unable to create product."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data payload. Provide sku, name, and price."]);
    }
?>
