<?php
header('Content-Type: application/json');

// --- 1. CONFIGURACIÓN Y VALIDACIÓN DE ENTRADA ---
// Intenta conectar a la DB. Si falla, el 500 podría venir de aquí.
require_once '../src/db/db_connect.php'; 

// Call the function to get the PDO connection
$pdo = get_db_connection();

// Obtener parámetros de paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15; // Usar 15 como fallback

// Asegurar que el límite y la página sean válidos
if ($limit <= 0) $limit = 15;
if ($page <= 0) $page = 1;

// Calcular el offset
$offset = ($page - 1) * $limit;

// Obtener el término de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // --- 2. OBTENER EL TOTAL DE REGISTROS (PARA LA PAGINACIÓN) ---
    $sql_count = "SELECT COUNT(*) AS total FROM proveedores";
    $params_count = [];
    if (!empty($search)) {
        $sql_count .= " WHERE nombre_proveedor LIKE :search OR rif LIKE :search OR telefono LIKE :search OR correo LIKE :search OR ciudad LIKE :search";
        $params_count[':search'] = '%' . $search . '%';
    }
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params_count);
    $totalSuppliers = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

    // --- 3. OBTENER LOS PROVEEDORES DE LA PÁGINA ACTUAL (CON LIMIT Y OFFSET) ---
    $sql = "SELECT 
                ID_proveedor,
                nombre_proveedor,
                rif,
                telefono,
                correo,
                ciudad
            FROM 
                proveedores";
    $params = [];
    if (!empty($search)) {
        $sql .= " WHERE nombre_proveedor LIKE :search OR rif LIKE :search OR telefono LIKE :search OR correo LIKE :search OR ciudad LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    $sql .= " ORDER BY 
                nombre_proveedor ASC
            LIMIT :limit OFFSET :offset"; // <-- CLAVE: Implementación de Paginación

    $stmt = $pdo->prepare($sql);
    
    // Asignar parámetros para la consulta de la página
    if (!empty($search)) {
        $stmt->bindParam(':search', $params[':search'], PDO::PARAM_STR);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();

    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 4. DEVOLVER LA RESPUESTA JSON COMPLETA ---
    echo json_encode([
        'suppliers' => $suppliers,
        'totalSuppliers' => $totalSuppliers,
        'limit' => $limit,
        'currentPage' => $page
    ]);

} catch (PDOException $e) {
    // Manejo de errores de base de datos
    http_response_code(500);
    // IMPORTANTE: En producción, no muestres $e->getMessage() por seguridad. 
    // Para depuración es útil.
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]); 
} catch (Exception $e) {
    // Manejo de otros errores, por ejemplo, si db_connect.php falla
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

?>