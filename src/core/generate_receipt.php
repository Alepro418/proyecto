<?php
require('../../pdf/fpdf.php'); 
require_once('../db/db_connect.php'); 

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Acceso denegado.");
}

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_venta === 0) {
    die("ID de venta no válido.");
}

try {
    $pdo = get_db_connection();

    // 1. Consulta de la Venta y Cliente
    $stmt = $pdo->prepare("SELECT v.id_venta, v.fecha_venta, v.metodo_pago, v.referencia_pago, v.subtotal, v.total_venta, c.nombre_cliente, c.cedula_rif 
                        FROM venta v 
                        INNER JOIN clientes c ON v.id_cliente = c.id_cliente 
                        WHERE v.id_venta = ?");
    $stmt->execute([$id_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        die("La venta con ID $id_venta no existe.");
    }

    // 2. Consulta de los Productos Vendidos
    $stmtDetails = $pdo->prepare("SELECT dv.*, p.nombre_producto 
                                  FROM detalle_venta dv 
                                  INNER JOIN productos p ON dv.id_producto = p.id_producto 
                                  WHERE dv.id_venta = ?");
    $stmtDetails->execute([$id_venta]);
    $detalles = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

    // 3. Creación del PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // --- SECCIÓN DEL LOGO Y EMPRESA ---
    // Construimos la ruta absoluta para evitar errores de "file not found"
    $ruta_logo = realpath(__DIR__ . '/../../public/assets/logo3.png'); 

    if ($ruta_logo && file_exists($ruta_logo)) {
        // Image(ruta, x, y, ancho)
        $pdf->Image($ruta_logo, 10, 10, 35); 
    }

    // Posicionamos el texto a la derecha del logo (x=50)
    $pdf->SetXY(50, 12); 
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 7, utf8_decode('INVERSIONES SUPREMA PC'), 0, 1, 'L');
    
    $pdf->SetX(50);
    $pdf->SetFont('Arial', '', 9);
    $info_empresa = "RIF: J-50192281-0\n" .
                    "Local N, CC SANTIAGO, CALLE URDANETA\n" .
                    "Puerto Cabello, Carabobo\n" .
                    "Tel: 412-503-5670";
    $pdf->MultiCell(0, 4, utf8_decode($info_empresa), 0, 'L');
    
    $pdf->Ln(10);
    
    // Título Central
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'RECIBO DE VENTA', 0, 1, 'C');
    
    // Línea divisoria
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);
    
    // --- DATOS DEL RECIBO Y CLIENTE ---
    $pdf->SetFont('Arial', '', 11);
    
    // Fila 1
    $pdf->Cell(30, 7, 'No. Recibo:', 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(70, 7, str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT), 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(30, 7, 'Fecha:', 0, 0);
    $pdf->Cell(0, 7, $venta['fecha_venta'], 0, 1);
    
    // Fila 2
    $pdf->Cell(30, 7, 'Cliente:', 0, 0);
    $pdf->Cell(70, 7, utf8_decode($venta['nombre_cliente']), 0, 0);
    $pdf->Cell(30, 7, 'Cedula/RIF:', 0, 0);
    $pdf->Cell(0, 7, $venta['cedula_rif'], 0, 1);
    
    // Fila 3
    $pdf->Cell(30, 7, 'Metodo Pago:', 0, 0);
    $pdf->Cell(70, 7, utf8_decode($venta['metodo_pago']), 0, 1);
    
    if (!empty($venta['referencia_pago'])) {
        $pdf->Cell(30, 8, 'Referencia:', 0, 0);
        $pdf->Cell(0, 8, $venta['referencia_pago'], 0, 1);
    }

    $pdf->Ln(5);

    // --- TABLA DE ARTÍCULOS ---
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 220, 255); // Gris claro para el encabezado
    
    $pdf->Cell(20, 8, 'CANT', 1, 0, 'C', true);
    $pdf->Cell(95, 8, 'DESCRIPCION', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'PRECIO U.', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'SUBTOTAL', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 10);
    
    foreach ($detalles as $row) {
        $precio = $row['precio_unitario_venta'];
        $cantidad = $row['cantidad_producto'];
        $subtotal_item = $precio * $cantidad;

        $pdf->Cell(20, 7, $cantidad, 1, 0, 'C');
        $pdf->Cell(95, 7, utf8_decode($row['nombre_producto']), 1, 0, 'L');
        $pdf->Cell(35, 7, number_format($precio, 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(40, 7, number_format($subtotal_item, 2, ',', '.'), 1, 1, 'R');
    }

    // --- TOTAL ---
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Bs ' . number_format($venta['total_venta'], 2, ',', '.'), 1, 1, 'R', true);

    // --- PIE DE PÁGINA ---
    $pdf->Ln(15);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 5, utf8_decode('¡Gracias por preferirnos!'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, utf8_decode('Este documento es un comprobante de operación y no posee valor fiscal.'), 0, 1, 'C');

    $pdf->Output('I', 'Recibo_' . $id_venta . '.pdf');

} catch (Exception $e) {
    die("Error en el sistema: " . $e->getMessage());
}