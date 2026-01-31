<?php
    session_start();   
?>    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Registrar Venta</title>
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
                        <a href="reg_sale.php" class="nav-link active">
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
                <h1 class="h2">Registrar Nueva Venta</h1>
            </header>

            <main class="flex-grow-1">
                <div class="card">
                    <div class="card-body">
                        <form action="../src/core/process_sale.php" method="post">
                            <fieldset class="mb-3">
                                <legend class="h5 mb-3">Datos del Cliente y Venta General</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha de Venta:</label>
                                        <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_cliente" class="form-label">Nombre del Cliente:</label>
                                        <input type="text" id="nombre_cliente" name="nombre_cliente" required class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="dni" class="form-label">Cédula / RIF del Cliente:</label>
                                        <div class="input-group">
                                            <select id="doc_type" name="doc_type" class="form-select" style="max-width: 100px;">
                                                <option value="V" selected>V</option>
                                                <option value="E">E</option>
                                                <option value="J">J</option>
                                                <option value="G">G</option>
                                            </select>
                                            <input type="text" id="dni" name="dni" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pay_method" class="form-label">Método de Pago:</label>
                                        <select id="pay_method" name="pay_method" required class="form-select">
                                            <option value="">Seleccione...</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta de Debito">Tarjeta de Débito</option>
                                            <option value="Tarjeta de Credito">Tarjeta de Crédito</option>
                                            <option value="Transferencia">Transferencia Bancaria</option>
                                            <option value="Pago Movil">Pago Móvil</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3" id="referencia-pago-movil" style="display:none;">
                                    <label for="referencia" class="form-label">Número de Referencia:</label>
                                    <input type="text" id="referencia" name="referencia" class="form-control">
                                </div>
                            </fieldset>

                            <fieldset class="mb-3">
                                <legend class="h5 mb-3">Productos de la Venta</legend>
                                <div id="product-details-container">
                                    <div class="product-item border rounded p-3 mb-3" data-product-id="1">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="product_name_1" class="form-label">Nombre del producto:</label>
                                                <input type="text" id="product_name_1" name="product_name[]" list="products-list" class="form-control product-name-input" required>
                                                <datalist id="products-list"></datalist>
                                                <input type="hidden" name="product_id[]" class="product-id-input">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="product_price_1" class="form-label">Precio por unidad:</label>
                                                <input type="number" id="product_price_1" name="product_price[]" step="0.01" min="0" readonly required class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="product_quantity_1" class="form-label">Cantidad:</label>
                                                <input type="number" id="product_quantity_1" name="product_quantity[]" min="1" step="1" required class="form-control">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="product_subtotal_1" class="form-label">Subtotal:</label>
                                                <input type="number" id="product_subtotal_1" name="product_subtotal[]" step="0.01" min="0" readonly class="form-control">
                                            </div>
                                        </div>
                                        <span class="stock-info text-muted">Stock disponible: --</span>
                                    </div>
                                </div>
                                <button type="button" id="add-product" class="btn btn-secondary mb-3">Añadir Otro Producto</button>
                            </fieldset>
                            
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <h4 class="me-3">Total de la Venta: <span id="grand-total">0.00</span> Bs</h4>
                                <input type="hidden" name="total_venta" id="hidden_grand_total">
                                <input type="hidden" name="subtotal" id="hidden_subtotal">
                            </div>

                            <div class="d-grid">
                                <button name="reg_sale" type="submit" class="btn btn-primary">Registrar Venta</button>
                            </div>
                        </form>
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
    <script src="js/reg_sale.js"></script>
</body>
</html>
