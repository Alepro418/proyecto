<?php
require_once '../db/db_connect.php'; 

// Establecer la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

// 1. Verificar el método de solicitud (debe ser POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido.']);
    http_response_code(405); // 405 Method Not Allowed
    exit;
}

// 2. Obtener y sanitizar los datos del formulario
// El formulario no pide Proveedor, se asume un ID_PROVEEDOR por defecto o se busca uno existente.
// Para este ejemplo, requeriremos el ID_PROVEEDOR en la lógica del script o asumiremos que el producto ya tiene uno.
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT); // Este es el precio de compra (Precio_de_entrada)
$fecha_ingreso = date('Y-m-d'); // Fecha actual para la actualización y el registro de compra

// **NOTA:** Dado que el formulario JS solo envía product_id, quantity y price, 
// para registrar la compra, necesitamos un id_proveedor. 
// Para fines de ejemplo, usaremos el ID de proveedor actual del producto.

// 3. Validar los datos
if (!$product_id || $product_id <= 0) {
    echo json_encode(['error' => 'ID de producto inválido.']);
    exit;
}
if (!$quantity || $quantity <= 0) {
    echo json_encode(['error' => 'Cantidad de reabastecimiento inválida.']);
    exit;
}
if (!$price || $price <= 0) {
    echo json_encode(['error' => 'Precio de entrada (costo) inválido.']);
    exit;
}

try {
    // Iniciar una transacción
    $pdo->beginTransaction();
    
    // =========================================================================
    // PASO A: OBTENER DATOS DEL PRODUCTO ANTES DE LA ACTUALIZACIÓN
    // (Necesitamos el id_proveedor del producto)
    // =========================================================================
    $sql_get_product = "SELECT id_proveedor FROM productos WHERE id_producto = :product_id";
    $stmt_get = $pdo->prepare($sql_get_product);
    $stmt_get->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_get->execute();
    $product_info = $stmt_get->fetch(PDO::FETCH_ASSOC);

    if (!$product_info) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Producto no encontrado en el inventario.']);
        exit;
    }
    
    // Usar el ID de proveedor del producto. Si es NULL, la compra se registrará sin proveedor.
    $id_proveedor = $product_info['id_proveedor']; 
    $total_compra = $quantity * $price;

    // =========================================================================
    // PASO B: ACTUALIZAR STOCK Y PRECIO DEL PRODUCTO EN LA TABLA 'productos'
    // =========================================================================
    $sql_update_product = "
        UPDATE productos 
        SET 
            cantidad = cantidad + :quantity,
            precio_de_entrada = :price,
            fecha_de_ingreso = :fecha_ingreso
        WHERE 
            id_producto = :product_id
    ";
    
    $stmt_update = $pdo->prepare($sql_update_product);
    $stmt_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt_update->bindParam(':price', $price); // PDO detectará el tipo DECIMAL/FLOAT
    $stmt_update->bindParam(':fecha_ingreso', $fecha_ingreso);
    $stmt_update->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_update->execute();
    
    if ($stmt_update->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['error' => 'El producto no existe o no se pudo actualizar el inventario.']);
        exit;
    }

    // =========================================================================
    // PASO C: REGISTRAR LA COMPRA EN LA TABLA 'compras'
    // =========================================================================
    $sql_insert_compra = "
        INSERT INTO compras (id_proveedor, total_compra) 
        VALUES (:id_proveedor, :total_compra)
    ";
    
    $stmt_compra = $pdo->prepare($sql_insert_compra);
    $stmt_compra->bindParam(':id_proveedor', $id_proveedor, $id_proveedor === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt_compra->bindParam(':total_compra', $total_compra);
    $stmt_compra->execute();
    
    // Obtener el ID de la compra recién insertada
    $id_compra = $pdo->lastInsertId();

    // =========================================================================
    // PASO D: REGISTRAR EL DETALLE DE LA COMPRA EN LA TABLA 'detalle_compras'
    // =========================================================================
    $sql_insert_detalle = "
        INSERT INTO detalle_compras (id_compra, id_producto, cantidad_comprada, precio_unitario_compra) 
        VALUES (:id_compra, :id_producto, :cantidad_comprada, :precio_unitario_compra)
    ";
    
    $stmt_detalle = $pdo->prepare($sql_insert_detalle);
    $stmt_detalle->bindParam(':id_compra', $id_compra, PDO::PARAM_INT);
    $stmt_detalle->bindParam(':id_producto', $product_id, PDO::PARAM_INT);
    $stmt_detalle->bindParam(':cantidad_comprada', $quantity, PDO::PARAM_INT);
    $stmt_detalle->bindParam(':precio_unitario_compra', $price);
    $stmt_detalle->execute();

    // 7. Si todo fue bien, confirmar los cambios
    $pdo->commit();

    // Devolver una respuesta exitosa al JavaScript
    echo json_encode(['success' => true, 'message' => 'Reabastecimiento completado y compra registrada.']);

} catch (PDOException $e) {
    // Si algo sale mal, revertir todos los cambios de la transacción
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Devolver una respuesta de error al JavaScript y registrar el error
    error_log("Error de reabastecimiento (TRANSACCIÓN FALLIDA): " . $e->getMessage());
    echo json_encode(['error' => 'Error de base de datos al procesar el reabastecimiento.']);
    http_response_code(500); // 500 Internal Server Error
} catch (Exception $e) {
    // Manejar otras excepciones
    error_log("Error general: " . $e->getMessage());
    echo json_encode(['error' => 'Error interno del servidor.']);
    http_response_code(500);
}
?>