<?php
header('Content-Type: application/json');
require_once '../src/db/db_connect.php';
$pdo = get_db_connection();

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Construcción de la cláusula WHERE para la búsqueda
    $whereClause = '';
    $params = [];
    if (!empty($searchTerm)) {
        $whereClause = " WHERE p.Nombre_Producto LIKE :searchTerm OR p.Codigo LIKE :searchTerm2";
        $params[':searchTerm'] = '%' . $searchTerm . '%';
        $params[':searchTerm2'] = '%' . $searchTerm . '%';
    }

    // Obtener el número total de productos con el filtro de búsqueda
    $total_sql = "SELECT COUNT(*) FROM productos p" . $whereClause;
    $total_stmt = $pdo->prepare($total_sql);
    $total_stmt->execute($params);
    $totalProducts = $total_stmt->fetchColumn();

    // Consulta para obtener todos los campos de la tabla productos
    // CAMBIO AQUÍ: Orden descendente por ID_Producto
    $sql = "SELECT 
                p.ID_Producto,
                p.Codigo,
                p.Nombre_Producto,
                p.Precio_de_entrada,
                p.Precio_de_Salida,
                p.Cantidad,
                p.Stock_minimo,
                p.Fecha_de_Ingreso,
                p.Ubicacion,
                p.ID_Proveedor,
                (p.Cantidad <= p.Stock_minimo) AS low_stock
            FROM 
                productos p"
            . $whereClause . 
            " ORDER BY 
                p.ID_Producto ASC  -- CAMBIO: Orden descendente
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    // Bind de los parámetros de búsqueda y paginación
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val, PDO::PARAM_STR);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'totalProducts' => (int)$totalProducts,
        'limit' => $limit,
        'page' => $page,
        'totalPages' => ceil($totalProducts / $limit),
        'products' => $products
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>