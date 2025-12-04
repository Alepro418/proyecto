<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
header('Content-Type: application/json');
require_once '../src/db/db_connect.php';

$pdo = get_db_connection();

$data = json_decode(file_get_contents('php://input'), true);

// Validar que todos los datos necesarios estén presentes
if (isset($data['product_id']) && isset($data['quantity']) && isset($data['price'])) {
    $productId = $data['product_id'];
    $quantity = $data['quantity'];
    $price = $data['price'];

    // Validar que los datos sean numéricos y positivos
    if (!is_numeric($productId) || !is_numeric($quantity) || !is_numeric($price) || $quantity <= 0 || $price <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Datos inválidos. La cantidad y el precio deben ser números positivos.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Obtener información del producto (proveedor y cantidad actual)
        $stmt = $pdo->prepare('SELECT id_proveedor, cantidad FROM productos WHERE id_producto = :product_id');
        $stmt->execute(['product_id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado.']);
            $pdo->rollBack();
            exit;
        }

        $providerId = $product['id_proveedor'];
        $currentQuantity = $product['cantidad'];

        // 2. Insertar en la tabla 'compras'
        $totalCompra = $quantity * $price;
        $compraStmt = $pdo->prepare('INSERT INTO compras (id_proveedor, total_compra, Tipo_compra) VALUES (:id_proveedor, :total_compra, :tipo_compra)');
        $compraStmt->execute(['id_proveedor' => $providerId, 'total_compra' => $totalCompra, 'tipo_compra' => 'Reabastecimiento']);
        $compraId = $pdo->lastInsertId();

        // 3. Insertar en la tabla 'detalle_compras'
        $detalleStmt = $pdo->prepare('INSERT INTO detalle_compras (id_compra, id_producto, cantidad_comprada, precio_unitario_compra) VALUES (:id_compra, :id_producto, :cantidad, :precio)');
        $detalleStmt->execute([
            'id_compra' => $compraId,
            'id_producto' => $productId,
            'cantidad' => $quantity,
            'precio' => $price
        ]);

        // 4. Actualizar la cantidad y el precio de entrada en la tabla 'productos'
        $newQuantity = $currentQuantity + $quantity;
        $updateStmt = $pdo->prepare('UPDATE productos SET cantidad = :quantity, precio_de_entrada = :price, fecha_de_ingreso = :fecha_ingreso WHERE id_producto = :product_id');
        $updateStmt->execute([
            'quantity' => $newQuantity,
            'price' => $price,
            'fecha_ingreso' => date('Y-m-d'),
            'product_id' => $productId
        ]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Reabastecimiento registrado y producto actualizado.']);

    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        // Proporcionar un mensaje de error más detallado en un entorno de desarrollo
        // error_log('Error de base de datos: ' . $e->getMessage()); 
        echo json_encode(['success' => false, 'error' => 'Error de base de datos al procesar la solicitud.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos. Se requiere ID de producto, cantidad y precio.']);
}
?>