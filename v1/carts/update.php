<?php
// Required RESTful headers for preflight and payload delivery definitions
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Global project dependencies
require_once '../../config/database.php';
require_once '../../objects/cart.php';
include_once '../../config/session_config.php';

// Initiate secure session tracking bounds
start_secure_session();

// Enforce authentication gate
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Access denied. Please log in to update item quantities."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Read raw JSON data input streams cleanly
$data = json_decode(file_get_contents("php://input"));

// Strict payload format verification matrix
if (!isset($data->product_id) || !isset($data->quantity) || !is_numeric($data->quantity) || (int)$data->quantity <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request: Missing fields or invalid data type formats for target parameters."]);
    exit();
}

$userId = $_SESSION['user_id'];
$sessionId = session_id();

try {
    $cartId = $cart->getOrCreateCart($userId, $sessionId);

    // Context Isolation Step: Validate if the product actually belongs to this specific cart first
    $itemCheck = "SELECT id FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1";
    $iStmt = $db->prepare($itemCheck);
    $iStmt->bindParam(":cart_id", $cartId, PDO::PARAM_INT);
    $iStmt->bindParam(":product_id", $data->product_id, PDO::PARAM_INT);
    $iStmt->execute();

    if (!$iStmt->fetch()) {
        http_response_code(404);
        echo json_encode(["message" => "Not Found: The target product does not exist within your active cart footprint."]);
        exit();
    }

    // Business Logic Check: Query database asset catalog to ensure stock parameters hold true
    $stockCheck = "SELECT stock_level FROM products WHERE id = :product_id LIMIT 1";
    $sStmt = $db->prepare($stockCheck);
    $sStmt->bindParam(":product_id", $data->product_id, PDO::PARAM_INT);
    $sStmt->execute();
    $product = $sStmt->fetch(PDO::FETCH_ASSOC);

    if ((int)$data->quantity > (int)$product['stock_level']) {
        http_response_code(422);
        echo json_encode(["message" => "Unprocessable Entity: Modified allocation parameters exceed available catalog inventory."]);
        exit();
    }

    // Standard ANSI SQL Update Query targeting only the verified item combination
    $query = "UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":quantity", $data->quantity, PDO::PARAM_INT);
    $stmt->bindParam(":cart_id", $cartId, PDO::PARAM_INT);
    $stmt->bindParam(":product_id", $data->product_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "OK: Cart item quantity updated successfully."]);
    }

} catch (PDOException $e) {
    // Suppress system traces from exposing internal user folders while giving debugging info to client suites
    error_log("Database Error in update.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "message" => "Internal Server Error: Failed to perform database execution handshakes.",
        "debug_error" => $e->getMessage(),
        "debug_file" => $e->getFile(),
        "debug_line" => $e->getLine()
    ]);
}
?>