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
    $stmt = $pdo->prepare("SELECT v.*, c.nombre_cliente, c.cedula_rif 
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
    $pdf->SetFont('Arial', 'B', 16);
    
    // Encabezado
    $pdf->Cell(0, 10, utf8_decode('RECIBO DE VENTA - PRAXIS'), 0, 1, 'C');
    $pdf->Ln(10);
    
    // Información corregida según tu SQL (todo en minúsculas)
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(40, 8, 'No. Venta:', 0, 0);
    $pdf->Cell(0, 8, $venta['id_venta'], 0, 1);
    
    $pdf->Cell(40, 8, 'Fecha:', 0, 0);
    $pdf->Cell(0, 8, $venta['fecha_venta'], 0, 1);
    
    $pdf->Cell(40, 8, 'Cliente:', 0, 0);
    $pdf->Cell(0, 8, utf8_decode($venta['nombre_cliente']), 0, 1);
    
    $pdf->Cell(40, 8, 'Cedula/RIF:', 0, 0);
    $pdf->Cell(0, 8, $venta['cedula_rif'], 0, 1);
    
    $pdf->Cell(40, 8, 'Metodo Pago:', 0, 0);
    $pdf->Cell(0, 8, utf8_decode($venta['metodo_pago']), 0, 1);
    $pdf->Ln(10);

    // Tabla de productos
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(80, 10, 'Producto', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Cant.', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Precio Unit.', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 11);
    foreach ($detalles as $row) {
        $pdf->Cell(80, 8, utf8_decode($row['nombre_producto']), 1);
        $pdf->Cell(30, 8, $row['cantidad_producto'], 1, 0, 'C');
        $pdf->Cell(40, 8, number_format($row['precio_unitario_venta'], 2), 1, 0, 'R');
        $pdf->Cell(40, 8, number_format($row['subtotal'], 2), 1, 1, 'R');
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Bs ' . number_format($venta['total_venta'], 2), 1, 1, 'R');

    $pdf->Output('I', 'Recibo_' . $id_venta . '.pdf');

} catch (Exception $e) {
    die("Error crítico: " . $e->getMessage());
}