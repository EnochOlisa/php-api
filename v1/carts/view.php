<?php
// Required RESTful response and security access headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Global project dependencies
require_once '../../config/database.php';
require_once '../../objects/cart.php';
include_once '../../config/session_config.php';

// Include session configuration and start a secure session
start_secure_session();

// Enforce authentication gate before allowing shopping cart access
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Access denied. Please log in to view your cart items."]);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

$userId = $_SESSION['user_id'];
$sessionId = session_id();

try {
    // Resolve or initialize the active cart record safely
    $cartId = $cart->getOrCreateCart($userId, $sessionId);

    // Relational table join optimized with strict standard PostgreSQL syntax
    // Explicit multiplication calculation handled inside the engine as a float decimal map
    $query = "SELECT ci.product_id, p.name, p.sku, p.price, ci.quantity, 
                     CAST(p.price * ci.quantity AS NUMERIC(10,2)) AS subtotal 
              FROM cart_items ci
              INNER JOIN products p ON ci.product_id = p.id
              WHERE ci.cart_id = :cart_id 
              ORDER BY ci.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":cart_id", $cartId, PDO::PARAM_INT);
    $stmt->execute();

    $cartItems = [];
    $grandTotal = 0.00;

    // Iterate through record streams and cast properties cleanly to align with strict typings
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $item = [
            "product_id" => (int)$row['product_id'],
            "name"       => $row['name'],
            "sku"        => $row['sku'],
            "price"      => (float)$row['price'],
            "quantity"   => (int)$row['quantity'],
            "subtotal"   => (float)$row['subtotal']
        ];
        $grandTotal += $item['subtotal'];
        $cartItems[] = $item;
    }

    // Format final grand total property value to exactly two decimal places
    $grandTotal = round($grandTotal, 2);

    // Emit 200 OK along with structured nested object payload
    http_response_code(200);
    echo json_encode([
        "cart_id" => (int)$cartId,
        "items" => $cartItems,
        "grand_total" => $grandTotal
    ]);

} catch (PDOException $e) {
    // Log complete internal exceptions inside the system error logs safely
    error_log("Database Error in view.php: " . $e->getMessage());

    // Mask sensitive directory paths while serving error traces to your test framework
    http_response_code(500);
    echo json_encode([
        "message" => "Internal Server Error: Failed to perform database execution handshakes."
    ]);
}
?>