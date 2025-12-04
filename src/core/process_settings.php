<?php
session_start();
require_once '../db/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['error' => 'No autorizado']);
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    http_response_code(405);
    exit;
}

$userId = $_SESSION['user_id'];
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = $_POST['password'] ?? null;
$theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_STRING);

// For now, we are not handling notifications
// $notifications = filter_input(INPUT_POST, 'notifications', FILTER_SANITIZE_STRING);

$pdo = get_db_connection();
$response = [];

try {
    $pdo->beginTransaction();

    // Update username
    if (!empty($username)) {
        $sql = "UPDATE usuario SET nombre_usuario = :username WHERE id = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $response['success_username'] = 'Nombre de usuario actualizado correctamente.';
        } else {
            throw new Exception("Error al actualizar el nombre de usuario.");
        }
    }

    // Update password
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET contraseña = :password WHERE id = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $response['success_password'] = 'Contraseña actualizada correctamente.';
        } else {
            throw new Exception("Error al actualizar la contraseña.");
        }
    }

    // Update theme
    if (!empty($theme) && in_array($theme, ['light', 'dark'])) {
        $sql = "UPDATE usuario SET theme = :theme WHERE id = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':theme', $theme, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $_SESSION['theme'] = $theme;
            $response['success_theme'] = 'Tema actualizado correctamente.';
        } else {
            throw new Exception("Error al actualizar el tema.");
        }
    }

    $pdo->commit();
    $response['success'] = true;
    echo json_encode($response);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error en process_settings.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error en el servidor al procesar la solicitud.']);
    http_response_code(500);
}
?>