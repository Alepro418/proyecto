<?php
    session_start();   
?>    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Dashboard</title>
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
        .card-icon {
            font-size: 3rem;
            opacity: 0.5;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
                        <a href="index.php" class="nav-link active" aria-current="page">
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
                        <a href="shopping.php" class="nav-link text-white">
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
            <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <h1 class="h2">Panel principal</h1>
            </header>

            <main class="flex-grow-1">
                <div class="row g-4">
                    <!-- Inventario -->
                    <div class="col-md-6 col-lg-4">
                        <a href="inventory.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-box-seam card-icon text-primary"></i>
                                    <h5 class="card-title mt-3">Inventario</h5>
                                    <p class="card-text">Gestiona tus productos, stock y categorías.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Ventas -->
                    <div class="col-md-6 col-lg-4">
                        <a href="sales.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-cart-check-fill card-icon text-success"></i>
                                    <h5 class="card-title mt-3">Ventas</h5>
                                    <p class="card-text">Consulta el historial de ventas y detalles.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Proveedores -->
                    <div class="col-md-6 col-lg-4">
                        <a href="suppliers.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-truck card-icon text-info"></i>
                                    <h5 class="card-title mt-3">Proveedores</h5>
                                    <p class="card-text">Administra la información de tus proveedores.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Alarmas -->
                    <div class="col-md-6 col-lg-4">
                        <a href="alarms.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-bell-fill card-icon text-danger"></i>
                                    <h5 class="card-title mt-3">Alarmas</h5>
                                    <p class="card-text">Revisa notificaciones de stock bajo y más.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Reportes -->
                    <div class="col-md-6 col-lg-4">
                        <a href="reports.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-file-earmark-bar-graph-fill card-icon text-warning"></i>
                                    <h5 class="card-title mt-3">Reportes</h5>
                                    <p class="card-text">Genera reportes de ventas, inventario, etc.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Compras -->
                    <div class="col-md-6 col-lg-4">
                        <a href="shopping.php" class="text-decoration-none">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-bag-plus-fill card-icon text-secondary"></i>
                                    <h5 class="card-title mt-3">Compras</h5>
                                    <p class="card-text">Registra y consulta las compras a proveedores.</p>
                                </div>
                            </div>
                        </a>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>