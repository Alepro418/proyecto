<?php
    session_start();
    require '../src/db/db_connect.php';
    $pdo = get_db_connection();
    
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
                proveedores pr ON c.ID_Proveedor = pr.ID_Proveedor
            ORDER BY
                c.Fecha_compra DESC
            LIMIT 10"; // Muestra las 10 compras más recientes

    try {
        $result = $pdo->query($sql);
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
?>    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Compras</title>
    <link rel="shortcut-icon" href="assets/logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar-fixed d-none d-md-flex bg-dark">
            <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-content" style="width: 100%; min-height: 100vh;">
                <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="assets/logo.png" alt="Logo" width="40" height="40" class="rounded-circle me-2">
                    <span class="fs-4">Praxis</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link text-white" aria-current="page">
                            <i class="bi bi-house-door-fill me-2"></i>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="inventory.php" class="nav-link text-white">
                            <i class="bi bi-box-seam me-2"></i>
                            Inventario
                        </a>
                    </li>
                    <li>
                        <a href="suppliers.php" class="nav-link text-white">
                            <i class="bi bi-truck me-2"></i>
                            Proveedores
                        </a>
                    </li>
                    <li>
                        <a href="sales.php" class="nav-link text-white">
                            <i class="bi bi-cart-check-fill me-2"></i>
                            Ventas
                        </a>
                    </li>
                    <li>
                        <a href="alarms.php" class="nav-link text-white">
                            <i class="bi bi-bell-fill me-2"></i>
                            Alarmas
                        </a>
                    </li>
                    <li>
                        <a href="reports.php" class="nav-link text-white">
                            <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>
                            Reportes
                        </a>
                    </li>
                    <li>
                        <a href="shopping.php" class="nav-link active">
                            <i class="bi bi-bag-plus-fill me-2"></i>
                            Compras
                        </a>
                    </li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li>
                        <a href="add_product.php" class="nav-link text-white">
                            <i class="bi bi-plus-circle-fill me-2"></i>
                            Agregar Producto
                        </a>
                    </li>
                    <li>
                        <a href="reg_sale.php" class="nav-link text-white">
                            <i class="bi bi-journal-plus me-2"></i>
                            Registrar Venta
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="backup.php" class="nav-link text-white">
                            <i class="bi bi-database-fill me-2"></i>
                            Copias de Seguridad
                        </a>
                    </li>
                    <li>
                        <a href="assets/manual_usuario.pdf" class="nav-link text-white" download="manual_usuario.pdf">
                            <i class="bi bi-file-earmark-pdf-fill me-2"></i>
                            Descargar manual
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="nav-link text-white">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Acerca de
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/user_icon.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong>
                            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado'; ?>
                        </strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <li><a class="dropdown-item" href="../src/auth/logout.php">Cerrar sesión</a></li>
                        <?php else: ?>
                        <li><a class="dropdown-item" href="sign_in.html">Iniciar sesión</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto p-4 d-flex flex-column">
            <header class="pb-3 mb-4 border-bottom">
                <h1 class="h2">Historial de Compras</h1>
            </header>

            <main class="flex-grow-1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="input-group" style="width: 40%;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" id="search-input" class="form-control" placeholder="Buscar por nombre del proveedor.">
                        </div>
                        <div class="input-group" style="width: 30%;">
                            <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                            <input type="date" id="date-input" class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                         <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Proveedor</th>
                                        <th>Cantidad</th>
                                        <th>Precio por unidad</th>
                                        <th>Total Compra</th>
                                        <th>Tipo de compra</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                            if ($result->rowCount() > 0) {
                                // Recorrer los resultados y mostrar en la tabla
                                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . $row["Fecha_compra"] . "</td>";
                                    echo "<td>" . htmlspecialchars($row["Nombre_Producto"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["Nombre_proveedor"]) . "</td>";
                                    echo "<td>" . $row["Cantidad_comprada"] . "</td>";
                                    echo "<td>Bs" . number_format($row["Precio_unitario_compra"], 2) . "</td>";
                                    echo "<td>Bs" . number_format($row["Total_compra"], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["Tipo_compra"]) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No se encontraron compras.</td></tr>";
                            }
                            ?>
                        </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div id="pagination-controls"></div>
                    </div>
                </div>
            </main>

            <footer class="pt-4 my-md-5 pt-md-5 border-top">
                <div class="row">
                    <div class="col-12 col-md text-center">
                        <small class="d-block mb-3 text-muted">&copy; 2024-2025 Praxis. Todos los derechos reservados.</small>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/utils.js"></script>
    <script src="js/shopping.js"></script>
</body>
</html>