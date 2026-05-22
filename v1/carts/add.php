<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
require_once '../../objects/cart.php';
include_once '../../config/session_config.php';

// Include session configuration and start a secure session
start_secure_session();

// Check if the user is authenticated before allowing profile updates
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Access denied. Please log in to add this product."]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Parse input payload configurations cleanly
$data = json_decode(file_get_contents("php://input"));

if (empty($data->product_id) || empty($data->quantity) || (int)$data->quantity <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Bad Request: Missing or invalid product_id or quantity settings."]);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

try {
    // Confirm target product entity exists inside database tables first
    $productCheck = "SELECT id, stock_level FROM products WHERE id = :product_id LIMIT 1";
    $pStmt = $db->prepare($productCheck);
    $pStmt->bindParam(":product_id", $data->product_id, PDO::PARAM_INT);
    $pStmt->execute();
    $product = $pStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(["message" => "Not Found: Targeted product asset identifier does not exist."]);
        exit();
    }

    // Verify quantity limits safely against stock limits
    if ((int)$data->quantity > (int)$product['stock_level']) {
        http_response_code(422);
        echo json_encode(["message" => "Unprocessable Entity: Requested quantity exceeds available inventory limits."]);
        exit();
    }

    $cartId = $cart->getOrCreateCart($userId, $sessionId);

    // FIX: Converted from MySQL "ON DUPLICATE KEY" to PostgreSQL "ON CONFLICT"
    $query = "INSERT INTO cart_items (cart_id, product_id, quantity) 
              VALUES (:cart_id, :product_id, :quantity)
              ON CONFLICT (cart_id, product_id) 
              DO UPDATE SET quantity = cart_items.quantity + EXCLUDED.quantity";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":cart_id", $cartId, PDO::PARAM_INT);
    $stmt->bindParam(":product_id", $data->product_id, PDO::PARAM_INT);
    $stmt->bindParam(":quantity", $data->quantity, PDO::PARAM_INT);
    // Note: ':quantity_update' binding removed because PostgreSQL uses 'EXCLUDED.quantity' automatically

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Created: Item attached and synced inside data structure cleanly."]);
    } else {
        throw new PDOException("Execution driver error on cart write pipeline sequence.");
    }

} catch (PDOException $e) {
    // TEMPORARY DEBUGGING: Output the exact database engine string
    http_response_code(500);
    echo json_encode([
        "message" => "Internal Server Error: Failed to perform database execution handshakes."
            ]);
}
?>