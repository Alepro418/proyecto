<?php
header('Content-Type: application/json');
require_once '../src/db/db_connect.php';

$pdo = get_db_connection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID not provided']);
    exit;
}

$productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($productId === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Product ID']);
    exit;
}

try {
    $sql = "SELECT id, name, description, price, stock FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

?>
