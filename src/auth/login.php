<?php
// Start a session to manage user login state
session_start();

// 1. Database Connection Details
// Includes the PDO connection from db_connect.php
require_once '../db/db_connect.php';

// Call the function to get the PDO connection
$pdo = get_db_connection();

// 2. Process Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize user input
    $nombre_ingresado = htmlspecialchars($_POST['user']);
    $contrasena_ingresada = htmlspecialchars($_POST['password']);

    try {
        // 3. Prepare SQL Statement to retrieve user data
        $sql = "SELECT ID, Nombre_usuario, Contraseña FROM usuario WHERE Nombre_usuario = :username";
        $stmt = $pdo->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':username', $nombre_ingresado, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // 4. Verify User and Password
        if ($stmt->rowCount() === 1) {
            // User found, fetch their data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashed_password_from_db = $user['Contraseña'];

            // Verify the entered password against the hashed password
            if (password_verify($contrasena_ingresada, $hashed_password_from_db)) {
                // Password is correct, user is logged in!
                // Set session variables to remember the user
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['Nombre_usuario']; // Corrected to Nombre_usuario
                $_SESSION['loggedin'] = true;

                // Redirect to a dashboard or a protected page
                header("Location: ../../public/index.php");
                exit();
            } else {
                // Password incorrect
                header("Location: ../../public/sign_in.html?error=1");
                exit();
            }
        } else {
            // User not found
            header("Location: ../../public/sign_in.html?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    // If the form wasn't submitted via POST, redirect or show an error
    header("Location: ../../public/sign_in.html");
    exit();
}

// No need to close the connection manually with PDO when the script ends
?>