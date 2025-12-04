<?php
require_once '../src/db/db_connect.php';

$pdo = get_db_connection();

header('Content-Type: application/json');

try {
    // Se asume que la tabla productos tiene una columna 'stock_minimo' para definir el umbral.
    // Si no la tienes, puedes usar un valor fijo como 'WHERE p.cantidad <= 10'.
    $sql = "SELECT 
                p.nombre_producto AS Producto, -- Se renombra la columna a 'Producto' como espera el JS
                p.cantidad AS Cantidad
            FROM productos p 
            WHERE p.cantidad <= 10 -- O puedes compararlo con una columna como p.stock_minimo
            ORDER BY p.cantidad ASC";
    $stmt = $pdo->query($sql);
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los productos con bajo stock: ' . $e->getMessage()]);
}
?>