<?php
// Set headers for a RESTful JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include necessary files using the established directory structure
include_once '../../config/database.php';
include_once '../../objects/product.php';

try {
    // Dependency Object Injection Setup
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);

    $isFound = false;

    // Adaptive Parameter Strategy and Validation

    // Scenario A: Lookup by Internal Primary Key Database ID (e.g., view.php?id=42)
    if (isset($_GET['id']) && $_GET['id'] !== '') {
        $targetId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if ($targetId === false) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => "error",
                "message" => "Invalid ID parameter format. Must be a valid integer."
            ]);
            exit();
        }

        $product->id = $targetId;
        $isFound = $product->readOneByID();

        // Scenario B: Lookup by Unique Business Barcode SKU (e.g., view.php?sku=9780132350884)
    } elseif (isset($_GET['sku']) && $_GET['sku'] !== '') {
        $targetSku = trim($_GET['sku']);

        if (!preg_match('/^\d{13}$/', $targetSku)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => "error",
                "message" => "Invalid SKU parameter format. Must be exactly 13 numeric digits."
            ]);
            exit();
        }

        $product->sku = $targetSku;
        $isFound = $product->readOneBySku();

        // Scenario C: Catch missing operational properties completely
    } else {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Incomplete request parameters. You must supply either an 'id' or a 'sku' to view the product."
        ]);
        exit();
    }

    // Resource State Serialization Outbound Contract
    if ($isFound) {
        $product_arr = [
            "id" => (int)$product->id,
            "sku" => $product->sku,
            "name" => $product->name,
            "description" => $product->description,
            "price" => (float)$product->price,
            "stock_level" => (int)$product->stock_level,
            "image_url" => $product->image_url,
            "created_at" => $product->created_at,
            "updated_at" => $product->updated_at
        ];

        http_response_code(200); // 200 OK
        echo json_encode($product_arr);
    } else {
        http_response_code(404); // 404 Not Found
        echo json_encode([
            "status" => "fail",
            "message" => "Product could not be located matching the specified identifier value."
        ]);
    }

} catch (PDOException $e) {
    // Keep internal network infrastructure metrics invisible to the client context
    error_log("Database Exception inside view.php router: " . $e->getMessage());

    http_response_code(500); // Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "An internal data layer problem prevented processing."
    ]);
} catch (Throwable $e) {
    error_log("Fatal Engine Runtime Error in view.php router: " . $e->getMessage());

    http_response_code(500); // Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "An unexpected internal service error occurred."
    ]);
}
?>