<?php
session_start();
require_once '../db/db_connect.php';

// Verificar autenticación
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Acceso no autorizado']);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Método no permitido']);
    exit;
}

// Verificar si se subió archivo
if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'No se recibió archivo válido']);
    exit;
}

$file = $_FILES['backup_file'];

// Validaciones
$max_size = 10 * 1024 * 1024; // 10MB

if ($file['size'] > $max_size) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Archivo demasiado grande (máximo 10MB)']);
    exit;
}

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($extension !== 'sql') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Solo se permiten archivos .sql']);
    exit;
}

try {
    // Obtener conexión
    $pdo = get_db_connection();
    
    // Leer contenido del archivo
    $sql_content = file_get_contents($file['tmp_name']);
    
    // Validar que sea SQL
    if (strpos($sql_content, 'CREATE TABLE') === false && 
        strpos($sql_content, 'INSERT INTO') === false &&
        strpos($sql_content, 'DROP TABLE') === false) {
        throw new Exception('El archivo no parece ser un backup SQL válido');
    }
    
    // Deshabilitar claves foráneas temporalmente
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Ejecutar el SQL en transacción
    $pdo->beginTransaction();
    
    // Separar las sentencias SQL (mejor que ejecutar todo de una vez)
    $sql_commands = explode(';', $sql_content);
    
    foreach ($sql_commands as $command) {
        $command = trim($command);
        if (!empty($command) && strlen($command) > 5) { // Ignorar líneas muy cortas
            $pdo->exec($command . ';');
        }
    }
    
    $pdo->commit();
    
    // Rehabilitar claves foráneas
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    // Crear directorio de logs si no existe
    if (!is_dir('../../logs')) {
        mkdir('../../logs', 0755, true);
    }
    
    // Registrar en logs
    $log_entry = date('Y-m-d H:i:s') . " - Usuario: " . ($_SESSION['username'] ?? 'Desconocido') . " - Backup restaurado: " . $file['name'] . "\n";
    file_put_contents('../../logs/backup_audit.log', $log_entry, FILE_APPEND);
    
    // Respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Base de datos restaurada exitosamente'
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($pdo)) {
        try {
            $pdo->rollBack();
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        } catch (Exception $rollbackError) {
            // Ignorar error en rollback
        }
    }
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al restaurar backup: ' . $e->getMessage()
    ]);
}
?>