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

    // 1. Consulta Maestra (Basada en tu SQL: tabla 'venta' y 'clientes')
    $stmt = $pdo->prepare("SELECT v.id_venta, v.fecha_venta, v.metodo_pago, v.referencia_pago, v.subtotal, v.total_venta, c.nombre_cliente, c.cedula_rif 
                        FROM venta v 
                        INNER JOIN clientes c ON v.id_cliente = c.id_cliente 
                        WHERE v.id_venta = ?");
    $stmt->execute([$id_venta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        die("La venta con ID $id_venta no existe en la base de datos.");
    }

    // 2. Consulta de Detalles (Basada en tu SQL: tabla 'detalle_venta' y 'productos')
    $stmtDetails = $pdo->prepare("SELECT dv.*, p.nombre_producto 
                                  FROM detalle_venta dv 
                                  INNER JOIN productos p ON dv.id_producto = p.id_producto 
                                  WHERE dv.id_venta = ?");
    $stmtDetails->execute([$id_venta]);
    $detalles = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

    // 3. Generación del PDF
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
    $pdf->Cell(0, 6, 'Telefono: 412-503-5670 | Email: contacto@inversionesuprema.com', 0, 1, 'C');
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
    $pdf->Cell(0, 8, $venta['fecha_venta'], 0, 1);
    
    $pdf->Cell(50, 8, 'Cliente:', 0, 0);
    $pdf->Cell(0, 8, $venta['nombre_cliente'], 0, 1);
    
    $pdf->Cell(50, 8, 'Cedula/RIF:', 0, 0);
    $pdf->Cell(0, 8, $venta['cedula_rif'], 0, 1);
    
    $pdf->Cell(50, 8, 'Metodo de Pago:', 0, 0);
    $pdf->Cell(0, 8, utf8_decode($venta['metodo_pago']), 0, 1);
    
    if (!empty($venta['referencia_pago'])) {
        $pdf->Cell(50, 8, 'Referencia:', 0, 0);
        $pdf->Cell(0, 8, $venta['referencia_pago'], 0, 1);
    }
    
    $pdf->Ln(10);

    // Encabezado de la tabla de productos
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(95, 8, 'Descripcion', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Precio Unit.', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Subtotal', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 10);
    $total = 0;

    $pdf->SetFont('Arial', '', 11);
    foreach ($detalles as $row) {
        $pdf->Cell(15, 8, $row['cantidad_producto'], 1, 0, 'C');
        $pdf->Cell(95, 8, utf8_decode($row['nombre_producto']), 1, 0, 'L');
        $pdf->Cell(40, 8, number_format($row['precio_unitario_venta'], 2, ',', 'R'), 1, 0, 'R');
        $pdf->Cell(40, 8, number_format($venta['subtotal'], 2, ',', '.'), 1, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Bs ' . number_format($venta['total_venta'], 2, ',', '.'), 1, 1, 'R');

    // Mensaje de agradecimiento
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, utf8_decode('¡Gracias por su compra!'), 0, 1, 'C');
    $pdf->Cell(0, 8, utf8_decode('Este documento no es valido como factura fiscal'), 0, 1, 'C');

    $pdf->Output('I', 'Recibo_' . $id_venta . '.pdf');

    

} catch (Exception $e) {
    die("Error crítico: " . $e->getMessage());
}