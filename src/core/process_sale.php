<?php
// process_sale.php

// Incluir la conexión a la base de datos con PDO
require_once '../db/db_connect.php';
// Incluir FPDF
require_once '../../pdf/fpdf.php';

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

// Función para generar el PDF del recibo
function generateReceiptPDF($venta_id, $pdo) {
    // Obtener información de la venta
    $sql_venta = "SELECT v.*, c.nombre_cliente, c.cedula_rif, DATE_FORMAT(v.fecha_venta, '%d/%m/%Y %H:%i') as fecha_formateada
                  FROM venta v 
                  JOIN clientes c ON v.id_cliente = c.id_cliente 
                  WHERE v.id_venta = ?";
    $stmt_venta = $pdo->prepare($sql_venta);
    $stmt_venta->execute([$venta_id]);
    $venta = $stmt_venta->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        throw new Exception("No se encontró la información de la venta.");
    }
    
    // DEBUG: Verificar qué datos estamos obteniendo
    // echo "<pre>Datos de la venta: ";
    // print_r($venta);
    // echo "</pre>";
    
    // Verificar que tenemos el método de pago
    if (!isset($venta['metodo_pago'])) {
        // Intentar con otro nombre de columna
        if (isset($venta['metodo_pago'])) {
            // Ya está bien
        } elseif (isset($venta['Metodo_pago'])) {
            $venta['metodo_pago'] = $venta['Metodo_pago'];
        } elseif (isset($venta['METODO_PAGO'])) {
            $venta['metodo_pago'] = $venta['METODO_PAGO'];
        } else {
            $venta['metodo_pago'] = 'No especificado';
        }
    }
    
    // Obtener detalles de los productos vendidos
    $sql_detalle = "SELECT dv.*, p.Nombre_Producto 
                    FROM detalle_venta dv 
                    JOIN productos p ON dv.ID_Producto = p.ID_Producto 
                    WHERE dv.ID_venta = ?";
    $stmt_detalle = $pdo->prepare($sql_detalle);
    $stmt_detalle->execute([$venta_id]);
    $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar que tenemos detalles
    if (empty($detalles)) {
        throw new Exception("No se encontraron detalles de productos para esta venta.");
    }
    
    // Crear instancia de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Configurar fuentes
    $pdf->SetFont('Arial', 'B', 16);
    
    // Encabezado del recibo
    $pdf->Cell(0, 10, 'RECIBO DE VENTA', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Información de la empresa (puedes personalizar esto)
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Inversiones Supreme PC', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Local N, CC SANTIAGO, CALLE URDANETA, 2 Nte., Puerto Cabello 2050, Carabobo', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Telefono: 000-000-0000 | Email: empresa@ejemplo.com', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Línea separadora
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);
    
    // Información del recibo
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 8, 'No. Recibo:', 0, 0);
    $pdf->Cell(0, 8, str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT), 0, 1);
    
    $pdf->Cell(50, 8, 'Fecha:', 0, 0);
    $pdf->Cell(0, 8, $venta['fecha_formateada'], 0, 1);
    
    $pdf->Cell(50, 8, 'Cliente:', 0, 0);
    $pdf->Cell(0, 8, $venta['nombre_cliente'], 0, 1);
    
    $pdf->Cell(50, 8, 'Cedula/RIF:', 0, 0);
    $pdf->Cell(0, 8, $venta['cedula_rif'], 0, 1);
    
    $pdf->Cell(50, 8, 'Metodo de Pago:', 0, 0);
    $pdf->Cell(0, 8, $venta['metodo_pago'], 0, 1);
    
    if (!empty($venta['referencia_pago'])) {
        $pdf->Cell(50, 8, 'Referencia:', 0, 0);
        $pdf->Cell(0, 8, $venta['referencia_pago'], 0, 1);
    }
    
    $pdf->Ln(10);
    
    // Encabezado de la tabla de productos
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(15, 10, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(95, 10, 'Descripcion', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Precio Unit.', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 10);
    $total = 0;
    
    // Detalles de productos
    foreach ($detalles as $detalle) {
        // Asegurar que tenemos los campos correctos
        $cantidad = $detalle['Cantidad_producto'] ?? $detalle['cantidad_producto'] ?? 0;
        $precio = $detalle['Precio_unitario_venta'] ?? $detalle['precio_unitario_venta'] ?? 0;
        $nombre = $detalle['Nombre_Producto'] ?? $detalle['nombre_producto'] ?? 'Producto';
        
        $subtotal = $cantidad * $precio;
        $total += $subtotal;
        
        $pdf->Cell(15, 8, $cantidad, 1, 0, 'C');
        $pdf->Cell(95, 8, $nombre, 1, 0, 'L');
        $pdf->Cell(40, 8, number_format($precio, 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(40, 8, number_format($subtotal, 2, ',', '.'), 1, 1, 'R');
    }
    
    // Total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(40, 10, number_format($total, 2, ',', '.'), 1, 1, 'R');
    
    $pdf->Ln(15);
    
    // Mensaje de agradecimiento
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, '¡Gracias por su compra!', 0, 1, 'C');
    $pdf->Cell(0, 8, 'Este documento no es valido como factura fiscal', 0, 1, 'C');
    
    // Generar nombre del archivo
    $filename = 'recibo_venta_' . str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT) . '.pdf';
    $filepath = '../pdf/recibos/' . $filename;
    
    // Crear directorio si no existe
    if (!file_exists('../pdf/recibos/')) {
        mkdir('../pdf/recibos/', 0777, true);
    }
    
    // Guardar el PDF
    $pdf->Output('F', $filepath);
    
    return [
        'filename' => $filename,
        'filepath' => $filepath,
        'venta_id' => $venta['id_venta']
    ];
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

        // 4. Generar el PDF del recibo
        $pdf_info = generateReceiptPDF($last_id_venta, $pdo);

        // Si todo fue bien, confirmar la transacción
        $pdo->commit();
        
        $message_type = 'success';
        $message_text = "¡Venta registrada exitosamente y stock actualizado!";
        $redirect_url = "../../public/sales.php"; // Redirigir al historial de ventas
        
        // Agregar enlace para descargar el PDF
        $pdf_download_url = "../pdf/recibos/" . $pdf_info['filename'];
        $message_text .= "<br><br><a href='" . htmlspecialchars($pdf_download_url) . "' target='_blank' style='color: #007bff; text-decoration: none; font-weight: bold;'>Descargar Recibo PDF</a>";

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
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        .message-container { 
            background-color: #fff; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); 
            text-align: center; 
            max-width: 500px; 
            width: 90%; 
        }
        .success-message { 
            color: #28a745; 
            font-size: 1.2em; 
            margin-bottom: 20px; 
        }
        .pdf-link {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            font-weight: bold;
        }
        .pdf-link:hover {
            background-color: #c82333;
        }
        p { 
            color: #333; 
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <p class="success-message"><?php echo $message_text; ?></p>
        <p>Serás redirigido en breve. Si no, haz clic <a href="<?php echo htmlspecialchars($redirect_url); ?>">aquí</a>.</p>
        <?php if (isset($pdf_info)): ?>
            <p>También puedes <a href="<?php echo htmlspecialchars($pdf_download_url); ?>" target="_blank" class="pdf-link">Descargar el Recibo PDF</a></p>
        <?php endif; ?>
    </div>
</body>
</html>