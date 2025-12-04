<?php
header('Content-Type: application/json');

require_once '../src/db/db_connect.php';

$pdo = get_db_connection();

$supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

if ($supplier_id === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de proveedor no válido.']);
    exit;
}

try {
    $sql = "SELECT 
                p.nombre_producto,
                p.cantidad
            FROM 
                productos p
            WHERE 
                p.ID_proveedor = :supplier_id
            ORDER BY 
                p.nombre_producto ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>