<?php
session_start();
require_once '../db/db_connect.php';
$pdo = get_db_connection();

// Function for styled error pages
function show_error_page($message, $redirect_url) {
    header('Content-Type: text/html; charset=utf-8');
    $safe_message = htmlspecialchars($message);
    $safe_redirect_url = htmlspecialchars($redirect_url);
    echo <<<HTML
<!DOCTYPE html>
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
        <p class='error-message'>{$safe_message}</p>
        <a href='{$safe_redirect_url}' class='back-button'>Volver al Registro</a>
    </div>
</body>
</html>
HTML;
    exit();
}

// Function for styled success pages
function show_success_page($message, $redirect_url) {
    header('Content-Type: text/html; charset=utf-8');
    $safe_message = htmlspecialchars($message);
    $safe_redirect_url = htmlspecialchars($redirect_url);
    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso Exitoso</title>
    <meta http-equiv="refresh" content="3;url={$safe_redirect_url}">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .message-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); text-align: center; max-width: 400px; width: 90%; }
        .success-message { color: #28a745; font-size: 1.2em; margin-bottom: 20px; }
        p { color: #333; }
    </style>
</head>
<body>
    <div class="message-container">
        <p class="success-message">{$safe_message}</p>
        <p>Serás redirigido en breve. Si no, haz clic <a href="{$safe_redirect_url}">aquí</a>.</p>
    </div>
</body>
</html>
HTML;
    exit();
}


$redirect_url = '../../public/add_product.html';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = htmlspecialchars(trim($_POST['codigoProducto']));
    $product = htmlspecialchars(trim($_POST['nombreProducto']));
    $entrancePrice = filter_var($_POST['precioEntrada'], FILTER_VALIDATE_FLOAT);
    $salePrice = filter_var($_POST['precioSalida'], FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    $entryDate = htmlspecialchars(trim($_POST['fechaIngreso']));
    $location = htmlspecialchars(trim($_POST['location']));
    $supplier = htmlspecialchars(trim($_POST['proveedor']));
    $rif = htmlspecialchars(trim($_POST['rif']));
    $tel = htmlspecialchars(trim($_POST['telefono']));
    $email = htmlspecialchars(trim($_POST['correo']));
    $city = htmlspecialchars(trim($_POST['ciudad']));

   if (empty($code) || empty($product) || $entrancePrice === false || $salePrice === false || $quantity === false || $quantity < 0 || empty($entryDate) || empty($location) || empty($supplier) || empty($rif)) {
    show_error_page("Error: Por favor, complete todos los campos obligatorios del producto y proveedor.", $redirect_url);
    }

    $pdo->beginTransaction();
try {
    $stmt_check_supplier = $pdo->prepare("SELECT ID_Proveedor FROM proveedores WHERE Rif = ? OR Nombre_proveedor = ?");
    $stmt_check_supplier->execute([$rif, $supplier]);
    $result_check_supplier = $stmt_check_supplier->fetch();

    if ($result_check_supplier) {
        $id_proveedor_actual = $result_check_supplier['ID_Proveedor'];
    } else {
        $stmt_insert_supplier = $pdo->prepare("INSERT INTO proveedores (Nombre_proveedor, Rif, Telefono, Correo, Ciudad) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt_insert_supplier->execute([$supplier, $rif, $tel, $email, $city])) {
            if ($stmt_insert_supplier->errorInfo()[1] == 1062) {
                throw new Exception("El proveedor con RIF '" . htmlspecialchars($rif) . "' o nombre '" . htmlspecialchars($supplier) . "' ya existe.");
            } else {
                throw new Exception("Error al insertar nuevo proveedor: " . $stmt_insert_supplier->errorInfo()[2]);
            }
        }
        $id_proveedor_actual = $pdo->lastInsertId();
    }

    // Insertar producto con code y location
    $stmt_insert_product = $pdo->prepare("INSERT INTO productos (Codigo, Nombre_Producto, Precio_de_entrada, Precio_de_Salida, Cantidad, Fecha_de_Ingreso, Ubicacion, ID_Proveedor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt_insert_product->execute([$code, $product, $entrancePrice, $salePrice, $quantity, $entryDate, $location, $id_proveedor_actual])) {
        if ($stmt_insert_product->errorInfo()[1] == 1062) {
            throw new Exception("Error: El producto '" . htmlspecialchars($product) . "' ya existe. Por favor, verifica el inventario.");
        } else {
            throw new Exception("Error al insertar producto: " . $stmt_insert_product->errorInfo()[2]);
        }
    }
    $id_producto_actual = $pdo->lastInsertId();

    // Registrar la compra
    $total_compra = $entrancePrice * $quantity;
    $stmt_insert_compra = $pdo->prepare("INSERT INTO compras (id_proveedor, total_compra, tipo_compra) VALUES (?, ?, ?)");
    if (!$stmt_insert_compra->execute([$id_proveedor_actual, $total_compra, 'Adquisicion'])) {
        throw new Exception("Error al registrar la compra: " . $stmt_insert_compra->errorInfo()[2]);
    }
    $id_compra_actual = $pdo->lastInsertId();

    // Registrar el detalle de la compra
    $stmt_insert_detalle = $pdo->prepare("INSERT INTO detalle_compras (id_compra, id_producto, cantidad_comprada, precio_unitario_compra) VALUES (?, ?, ?, ?)");
    if (!$stmt_insert_detalle->execute([$id_compra_actual, $id_producto_actual, $quantity, $entrancePrice])) {
        throw new Exception("Error al registrar el detalle de la compra: " . $stmt_insert_detalle->errorInfo()[2]);
    }

    $pdo->commit();
    show_success_page("Producto y proveedor procesados exitosamente.", "../../public/inventory.php");

} catch (Exception $e) {
    $pdo->rollBack();
    show_error_page("Error en el procesamiento: " . $e->getMessage(), $redirect_url);
}
} else {
    show_error_page("Acceso no permitido: El formulario debe enviarse por POST.", '../../public/add_product.html');
}