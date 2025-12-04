<?php

function get_db_connection() {
    // Details for database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "praxis";
    $charset = "utf8mb4";

    try {
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=$charset";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Re-throw the exception to be caught by the calling script
        throw $e;
    }
}

?>