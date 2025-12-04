<?php
// process_sale.php

// Incluir la conexión a la base de datos con PDO
require_once '../db/db_connect.php';

$pdo = get_db_connection();

// Variables para el mensaje de respuesta HTML
$message_type = 'error';
$message_text = '';
$redirect_url = '../../public/reg_sale.php'; // Ruta corregida para redirigir al formulario

// Función para mostrar un mensaje de error en formato HTML y terminar la ejecución
function show_error_page($message, $redirect_url) {
    echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Error en el Proceso</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
                    .message-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); text-align: center; max-width: 400px; width: 90%; }
                    .error-message { color: #dc3545; font-size: 1.2em; margin-bottom: 20px; }
                    .back-button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; font-size: 1em; }
                    .back-button:hover { background-color: #0056b3; }
                </style>
            </head>
            <body>
                <div class='message-container'>
                    <p class='error-message'>" . htmlspecialchars($message) . "</p>
                    <a href='" . htmlspecialchars($redirect_url) . "' class='back-button'>Volver al Registro</a>
                </div>
            </body>
            </html>";
    exit();
}

if (isset($_POST['reg_sale'])) {

    try {
        $pdo->beginTransaction(); // Inicia la transacción

        // Validar y obtener los datos de la venta general
        $fecha = $_POST['fecha'];
        $nombre_cliente = trim($_POST['nombre_cliente']);
        $doc_type = $_POST['doc_type'];
        $dni_number = trim($_POST['dni']);
        $dni = $doc_type . $dni_number; // Concatenar para formar, por ej., 'V12345678'
        $pay_method = $_POST['pay_method'];
        $referencia = !empty($_POST['referencia']) ? trim($_POST['referencia']) : null;
        $total_venta = filter_var($_POST['total_venta'], FILTER_VALIDATE_FLOAT);

        // Validaciones básicas
        if (empty($fecha) || empty($nombre_cliente) || empty($doc_type) || empty($dni_number) || empty($pay_method) || $total_venta === false || $total_venta < 0) {
            throw new Exception("Datos de la venta incompletos o inválidos.");
        }
        if (($pay_method === 'Pago Movil' || $pay_method === 'Transferencia') && empty($referencia)) {
            throw new Exception("El número de referencia es obligatorio para el método de pago seleccionado.");
        }

        // 1. Gestionar cliente (buscar o crear)
        $stmt_cliente = $pdo->prepare("SELECT id_cliente FROM clientes WHERE cedula_rif = ?");
        $stmt_cliente->execute([$dni]);
        $id_cliente = $stmt_cliente->fetchColumn();

        if (!$id_cliente) {
            // Si el cliente no existe, lo crea
            $sql_insert_cliente = "INSERT INTO clientes (nombre_cliente, cedula_rif) VALUES (?, ?)";
            $stmt_insert_cliente = $pdo->prepare($sql_insert_cliente);
            $stmt_insert_cliente->execute([$nombre_cliente, $dni]);
            $id_cliente = $pdo->lastInsertId();
        }

        // 2. Insertar datos en la tabla `venta` usando el id_cliente
        $sql_venta = "INSERT INTO venta (id_cliente, fecha_venta, metodo_pago, referencia_pago, total_venta) VALUES (?, ?, ?, ?, ?)";
        $stmt_venta = $pdo->prepare($sql_venta);
        $stmt_venta->execute([$id_cliente, $fecha, $pay_method, $referencia, $total_venta]);
        $last_id_venta = $pdo->lastInsertId();

        // 3. Procesar los detalles de los productos
        $product_ids = $_POST['product_id'] ?? [];
        $product_names = $_POST['product_name'] ?? [];
        $product_quantities = $_POST['product_quantity'] ?? [];
        $product_prices = $_POST['product_price'] ?? [];

        if (empty($product_ids)) {
            throw new Exception("No se ha añadido ningún producto a la venta.");
        }

        // Preparar consultas para `detalle_venta` y actualización de `productos`
        $sql_detalle = "INSERT INTO detalle_venta (ID_venta, ID_Producto, Cantidad_producto, Precio_unitario_venta) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $pdo->prepare($sql_detalle);

        $sql_update_stock = "UPDATE productos SET Cantidad = Cantidad - ? WHERE ID_Producto = ?";
        $stmt_update_stock = $pdo->prepare($sql_update_stock);

        $sql_check_stock = "SELECT Cantidad FROM productos WHERE ID_Producto = ?";
        $stmt_check_stock = $pdo->prepare($sql_check_stock);

        // Iterar sobre cada producto de la venta
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = filter_var($product_ids[$i], FILTER_VALIDATE_INT);
            $product_name = htmlspecialchars(trim($product_names[$i]));
            $product_quantity = filter_var($product_quantities[$i], FILTER_VALIDATE_INT);
            $product_price = filter_var($product_prices[$i], FILTER_VALIDATE_FLOAT);

            if ($product_id === false || empty($product_name) || $product_quantity === false || $product_quantity <= 0 || $product_price === false || $product_price < 0) {
                throw new Exception("Datos del producto inválidos en la línea " . ($i + 1));
            }

            // --- Validación de Stock Actual ---
            $stmt_check_stock->execute([$product_id]);
            $stock_data = $stmt_check_stock->fetch(PDO::FETCH_ASSOC);
            $current_stock = $stock_data ? $stock_data['Cantidad'] : 0;

            if ($product_quantity > $current_stock) {
                throw new Exception("Stock insuficiente para el producto '" . $product_name . "'. Disponible: " . $current_stock);
            }

            // --- Insertar en `detalle_venta` ---
            $stmt_detalle->execute([$last_id_venta, $product_id, $product_quantity, $product_price]);

            // --- Actualizar `Cantidad` en la tabla `productos` ---
            $stmt_update_stock->execute([$product_quantity, $product_id]);
        }

        // Si todo fue bien, confirmar la transacción
        $pdo->commit();
        $message_type = 'success';
        $message_text = "¡Venta registrada exitosamente y stock actualizado!";
        $redirect_url = "../../public/sales.php"; // Redirigir al historial de ventas

    } catch (Exception $e) {
        // Si algo falla, revertir la transacción
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Usar la función de error para mostrar el mensaje
        show_error_page("Error en el procesamiento: " . $e->getMessage(), $redirect_url);
    }
} else {
    $message_text = "Acceso no permitido: El formulario debe enviarse por POST.";
    show_error_page($message_text, $redirect_url);
}

// Generar la página HTML de respuesta (solo para éxito)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de la Venta</title>
    <?php if ($message_type === 'success' && $redirect_url): ?>
        <meta http-equiv="refresh" content="3;url=<?php echo htmlspecialchars($redirect_url); ?>">
    <?php endif; ?>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .message-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); text-align: center; max-width: 400px; width: 90%; }
        .success-message { color: #28a745; font-size: 1.2em; margin-bottom: 20px; }
        p { color: #333; }
    </style>
</head>
<body>
    <div class="message-container">
        <p class="success-message"><?php echo htmlspecialchars($message_text); ?></p>
        <p>Serás redirigido en breve. Si no, haz clic <a href="<?php echo htmlspecialchars($redirect_url); ?>">aquí</a>.</p>
    </div>
</body>
</html>
