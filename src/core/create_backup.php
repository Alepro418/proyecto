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

// Desactivar errores para no ensuciar el archivo SQL
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Obtener conexión para leer configuración
    $pdo = get_db_connection();
    
    // Configuración (ajusta según tu entorno)
    // NOTA: Deberías obtener estos valores de db_connect.php o configuración
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'praxis';
    
    // Nombre del archivo
    $backup_file = 'backup_praxis_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Comando mysqldump - DOS OPCIONES:
    
    // OPCIÓN 1: Usar exec() (más rápido)
    $command = "mysqldump --host={$host} --user={$user} --password={$pass} {$dbname}";
    
    // OPCIÓN 2: Ruta completa (si exec() no funciona)
    // Windows: C:\\xampp\\mysql\\bin\\mysqldump.exe
    // Linux: /usr/bin/mysqldump
    // $command = "C:\\xampp\\mysql\\bin\\mysqldump.exe --host={$host} --user={$user} --password={$pass} {$dbname}";
    
    // Ejecutar
    $output = [];
    $return_var = 0;
    exec($command . " 2>&1", $output, $return_var);
    
    if ($return_var !== 0) {
        // Intentar método alternativo si exec falla
        $sql_content = backupWithPDO($pdo);
    } else {
        $sql_content = implode("\n", $output);
    }
    
    // Crear directorio de logs si no existe
    if (!is_dir('../../logs')) {
        mkdir('../../logs', 0755, true);
    }
    
    // Registrar en logs
    $log_entry = date('Y-m-d H:i:s') . " - Usuario: " . ($_SESSION['username'] ?? 'Desconocido') . " - Backup creado: {$backup_file}\n";
    file_put_contents('../../logs/backup_audit.log', $log_entry, FILE_APPEND);
    
    // Enviar como descarga
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_file . '"');
    header('Content-Length: ' . strlen($sql_content));
    
    echo $sql_content;
    
} catch (Exception $e) {
    // Asegurar que no se haya enviado output antes
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'error' => true,
        'message' => 'Error al crear backup: ' . $e->getMessage()
    ]);
    exit;
}

// Función alternativa si exec() no funciona
function backupWithPDO($pdo) {
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $sql_script = "";
    foreach ($tables as $table) {
        // Obtener estructura de la tabla
        $result = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch(PDO::FETCH_NUM);
        $sql_script .= "\n\n" . $row[1] . ";\n\n";
        
        // Obtener datos de la tabla
        $result = $pdo->query("SELECT * FROM `$table`");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $sql_script .= "INSERT INTO `$table` VALUES(";
            $values = [];
            foreach ($row as $value) {
                $values[] = $pdo->quote($value);
            }
            $sql_script .= implode(',', $values) . ");\n";
        }
    }
    
    return $sql_script;
}
?>