<?php
header('Content-Type: application/json');
require_once '../src/db/db_connect.php';

$pdo = get_db_connection();


// Basic input validation
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
$offset = ($page - 1) * $limit;

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Base SQL parts
    $sqlBase = "FROM venta v LEFT JOIN clientes c ON v.id_cliente = c.id_cliente";
    $whereClause = "";
    $params = [];

    if (!empty($search)) {
        $whereClause = " WHERE (c.nombre_cliente LIKE :search OR c.cedula_rif LIKE :search OR v.metodo_pago LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $date = isset($_GET['date']) ? trim($_GET['date']) : '';
    if (!empty($date)) {
        if (!empty($whereClause)) {
            $whereClause .= " AND DATE(v.fecha_venta) = :date";
        } else {
            $whereClause = " WHERE DATE(v.fecha_venta) = :date";
        }
        $params[':date'] = $date;
    }

    $paymentMethod = isset($_GET['payment_method']) ? trim($_GET['payment_method']) : '';
    if (!empty($paymentMethod)) {
        if (!empty($whereClause)) {
            $whereClause .= " AND v.metodo_pago = :payment_method";
        } else {
            $whereClause = " WHERE v.metodo_pago = :payment_method";
        }
        $params[':payment_method'] = $paymentMethod;
    }

    // Get total records for pagination
    $totalRecordsSql = "SELECT COUNT(*) $sqlBase $whereClause";
    $totalStmt = $pdo->prepare($totalRecordsSql);
    $totalStmt->execute($params);
    $totalRecords = $totalStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Get sales for the current page
    $sql = "SELECT v.id_venta AS ID_venta, v.fecha_venta AS Fecha_venta, c.nombre_cliente AS Nombre_cliente, c.cedula_rif AS Cedula_Rif, v.metodo_pago AS Metodo_pago, v.referencia_pago AS Referencia_pago, v.total_venta AS Total_venta $sqlBase $whereClause ORDER BY v.id_venta ASC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind params
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data with sales and pagination info
    echo json_encode([
        'sales' => $sales,
        'totalPages' => $totalPages
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

?>
