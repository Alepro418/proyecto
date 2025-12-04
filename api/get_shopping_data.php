<?php
header('Content-Type: application/json');
require '../src/db/db_connect.php';
$pdo = get_db_connection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT
            c.Fecha_compra,
            p.Nombre_Producto,
            pr.Nombre_proveedor,
            dc.Cantidad_comprada,
            dc.Precio_unitario_compra,
            c.Total_compra,
            c.Tipo_compra
        FROM
            compras c
        INNER JOIN
            detalle_compras dc ON c.ID_compra = dc.ID_compra
        INNER JOIN
            productos p ON dc.ID_Producto = p.ID_Producto
        INNER JOIN
            proveedores pr ON c.ID_Proveedor = pr.ID_Proveedor";

$countSql = "SELECT COUNT(DISTINCT c.ID_compra) as total
             FROM compras c
             INNER JOIN detalle_compras dc ON c.ID_compra = dc.ID_compra
             INNER JOIN productos p ON dc.ID_Producto = p.ID_Producto
             INNER JOIN proveedores pr ON c.ID_Proveedor = pr.ID_Proveedor";

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "pr.Nombre_proveedor LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($date)) {
    $whereClauses[] = "DATE(c.Fecha_compra) = :date";
    $params[':date'] = $date;
}

if (count($whereClauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
    $countSql .= " WHERE " . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY c.Fecha_compra DESC LIMIT :limit OFFSET :offset";

try {
    $stmt = $pdo->prepare($sql);
    $countStmt = $pdo->prepare($countSql);

    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
        $countStmt->bindParam($key, $val);
    }

    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt->execute();
    $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRows / $limit);

    echo json_encode([
        'purchases' => $purchases,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>