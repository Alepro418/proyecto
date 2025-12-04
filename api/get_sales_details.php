<?php
header('Content-Type: application/json');
require_once '../src/db/db_connect.php';

$pdo = get_db_connection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Sale ID not provided']);
    exit;
}

$saleId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($saleId === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Sale ID']);
    exit;
}

try {
    // 1. Get the main sale data
    $sql_sale = "SELECT 
                    v.*, 
                    c.nombre_cliente AS Nombre_cliente, 
                    c.cedula_rif AS Cedula_Rif
                FROM 
                    venta v
                LEFT JOIN 
                    clientes c ON v.id_cliente = c.id_cliente
                WHERE 
                    v.id_venta = :id";
    $stmt_sale = $pdo->prepare($sql_sale);
    $stmt_sale->bindParam(':id', $saleId, PDO::PARAM_INT);
    $stmt_sale->execute();
    $sale = $stmt_sale->fetch(PDO::FETCH_ASSOC);

    if (!$sale) {
        http_response_code(404);
        echo json_encode(['error' => 'Sale not found']);
        exit;
    }

    // 2. Get the sale details (line items)
    $sql_details = "SELECT 
                        dv.id_producto AS ID_Producto,
                        p.nombre_producto AS Nombre_Producto,
                        dv.cantidad_producto AS Cantidad_producto,
                        dv.precio_unitario_venta AS Precio_unitario_venta,
                        (dv.cantidad_producto * dv.precio_unitario_venta) AS Subtotal
                    FROM 
                        detalle_venta dv
                    LEFT JOIN 
                        productos p ON dv.id_producto = p.id_producto
                    WHERE 
                        dv.id_venta = :id";
    $stmt_details = $pdo->prepare($sql_details);
    $stmt_details->bindParam(':id', $saleId, PDO::PARAM_INT);
    $stmt_details->execute();
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

    // 3. Combine and return the final JSON
    $sale['details'] = $details;
    echo json_encode($sale);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

?>
