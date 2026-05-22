<?php
// Required RESTful response headers and security definitions
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Global project dependencies
require_once '../../config/database.php';
require_once '../../objects/cart.php';
include_once '../../config/session_config.php';

// Include session configuration and start a secure session
start_secure_session();

// Enforce authentication gate
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Access denied. Please log in to remove products from your cart."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Extract the target identifier from the URL query strings (?product_id=14)
$productId = $_GET['product_id'] ?? null;

// Validate incoming parameters strictly against type formatting mismatches
if (!$productId || !is_numeric($productId)) {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request: Missing or non-numeric product_id parameters."]);
    exit();
}

$userId = $_SESSION['user_id'];
$sessionId = session_id();

try {
    // Resolve the contextual cart mapping scope container cleanly
    $cartId = $cart->getOrCreateCart($userId, $sessionId);

    // Context Isolation Enforcement: The statement MUST link the cart_id and product_id
    // to prevent malicious tampering with other users' line items via direct record adjustments.
    $query = "DELETE FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":cart_id", $cartId, PDO::PARAM_INT);
    $stmt->bindParam(":product_id", $productId, PDO::PARAM_INT);
    $stmt->execute();

    // Use rowCount calculation matrices to track if a row was actually targeted and mutated
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(["message" => "OK: Product successfully removed from your active cart layout."]);
    } else {
        // If the query runs but zero rows match, the product didn't exist in this specific cart footprint
        http_response_code(404);
        echo json_encode(["message" => "Not Found: The targeted item does not exist inside your shopping cart context."]);
    }

} catch (PDOException $e) {
    // Intercept engine errors safely while passing structural trace properties up for your debugging matrix
    error_log("Database Error in remove.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "message" => "Internal Server Error: Failed to perform database execution handshakes."
    ]);
}
?>