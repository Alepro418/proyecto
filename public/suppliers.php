<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Proveedores</title>
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
        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }
        .loading-indicator {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .pagination-controls {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 15px;
        }
        .view-products-btn {
            white-space: nowrap;
        }
        .stock-low {
            background-color: #ffebee;
        }
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            .card-header .input-group {
                width: 100% !important;
            }
            .table-responsive {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar-fixed d-none d-md-flex bg-dark">
            <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-content" style="width: 100%; min-height: 100vh;">
                <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="assets/logo.jpg" alt="Logo" width="40" height="40" class="rounded-circle me-2">
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
                        <a href="suppliers.php" class="nav-link active">
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
                <h1 class="h2">Directorio de Proveedores</h1>
            </header>

            <main class="flex-grow-1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="input-group" style="width: 40%;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" id="search-input" class="form-control" placeholder="Buscar por nombre del proveedor.">
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2" id="total-suppliers">0 proveedores</span>
                            <div class="spinner-border spinner-border-sm text-primary ms-2" id="loading-indicator" style="display: none;" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="loading-indicator" id="table-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando proveedores...</span>
                            </div>
                            <p class="mt-2">Cargando proveedores...</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Proveedor</th>
                                        <th>RIF</th>
                                        <th>Teléfono</th>
                                        <th>Correo</th>
                                        <th>Ciudad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="suppliers-table-body">
                                    <!-- Los proveedores se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div id="pagination-controls" class="pagination-controls"></div>
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

    <!-- Products Modal -->
    <div class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productsModalLabel">Productos del Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 id="supplier-name" class="mb-0">Proveedor: <span id="current-supplier-name">-</span></h6>
                        <span class="badge bg-primary" id="products-count">0 productos</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                </tbody>
                        </table>
                    </div>
                    <div id="products-loading" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando productos...</span>
                        </div>
                        <p class="mt-2">Cargando productos...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Función para escapar HTML y prevenir XSS
        function escapeHTML(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            const suppliersPerPage = 15;

            const searchInput = document.getElementById('search-input');
            const loadingIndicator = document.getElementById('loading-indicator');
            const tableLoading = document.getElementById('table-loading');
            const totalSuppliersSpan = document.getElementById('total-suppliers');
            const suppliersTableBody = document.getElementById('suppliers-table-body');

            // Cargar proveedores iniciales
            fetchSuppliers(currentPage, '');

            searchInput.addEventListener('input', () => {
                fetchSuppliers(1, searchInput.value.trim());
            });

            async function fetchSuppliers(page, searchTerm = '') {
                currentPage = page;
                
                // Mostrar indicadores de carga
                loadingIndicator.style.display = 'inline-block';
                tableLoading.style.display = 'block';
                suppliersTableBody.innerHTML = '';

                try {
                    let url = `../api/get_suppliers_data.php?page=${page}&limit=${suppliersPerPage}`;
                    if (searchTerm) {
                        url += `&search=${encodeURIComponent(searchTerm)}`;
                    }
                    
                    const response = await fetch(url);
                    const data = await response.json();

                    suppliersTableBody.innerHTML = '';

                    if (data.error) {
                        suppliersTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${escapeHTML(data.error)}</td></tr>`;
                        totalSuppliersSpan.textContent = 'Error al cargar';
                        return;
                    }

                    if (data.suppliers.length === 0) {
                        suppliersTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No se encontraron proveedores.</td></tr>';
                        totalSuppliersSpan.textContent = '0 proveedores';
                    } else {
                        data.suppliers.forEach(supplier => {
                            const row = suppliersTableBody.insertRow();
                            row.innerHTML = `
                                <td>${escapeHTML(supplier.nombre_proveedor)}</td>
                                <td>${escapeHTML(supplier.rif)}</td>
                                <td>${escapeHTML(supplier.telefono)}</td>
                                <td>${escapeHTML(supplier.correo)}</td>
                                <td>${escapeHTML(supplier.ciudad)}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-products-btn" data-supplier-id="${supplier.ID_proveedor}" data-supplier-name="${escapeHTML(supplier.nombre_proveedor)}">
                                        <i class="bi bi-box-seam"></i> Ver Productos
                                    </button>   
                                </td>
                            `;
                        });
                        totalSuppliersSpan.textContent = `${data.totalSuppliers || 0} proveedores`;
                    }

                    renderPagination(data.totalSuppliers, suppliersPerPage, page, searchTerm);

                } catch (error) {
                    console.error("Fetch suppliers error:", error);
                    suppliersTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar los datos.</td></tr>';
                    totalSuppliersSpan.textContent = 'Error al cargar';
                } finally {
                    // Ocultar indicadores de carga
                    loadingIndicator.style.display = 'none';
                    tableLoading.style.display = 'none';
                }
            }

            // Event listener para el botón "Ver Productos"
            document.addEventListener('click', async function(event) {
                if (event.target.closest('.view-products-btn')) {
                    const button = event.target.closest('.view-products-btn');
                    const supplierId = button.dataset.supplierId;
                    const supplierName = button.dataset.supplierName;
                    
                    const productsTbody = document.getElementById('products-tbody');
                    const productsLoading = document.getElementById('products-loading');
                    const currentSupplierName = document.getElementById('current-supplier-name');
                    const productsCount = document.getElementById('products-count');
                    
                    // Actualizar nombre del proveedor en el modal
                    currentSupplierName.textContent = supplierName;
                    
                    // Mostrar loading
                    productsTbody.innerHTML = '';
                    productsLoading.style.display = 'block';
                    productsCount.textContent = 'Cargando...';

                    try {
                        const response = await fetch(`../api/get_supplier_products.php?supplier_id=${supplierId}`);
                        const products = await response.json();

                        productsTbody.innerHTML = '';

                        if (products.error) {
                            // 🗑️ CAMBIO: colspan a 2
                            productsTbody.innerHTML = `<tr><td colspan="2" class="text-center text-danger py-4">${escapeHTML(products.error)}</td></tr>`;
                            productsCount.textContent = 'Error';
                        } else if (products.length === 0) {
                            // 🗑️ CAMBIO: colspan a 2
                            productsTbody.innerHTML = '<tr><td colspan="2" class="text-center py-4">No se encontraron productos para este proveedor.</td></tr>';
                            productsCount.textContent = '0 productos';
                        } else {
                            products.forEach(product => {
                                const row = productsTbody.insertRow();
                                // Aplicar clase de stock bajo si es necesario
                                const stockClass = parseInt(product.cantidad) <= 10 ? 'stock-low' : '';
                                if (stockClass) {
                                    row.classList.add('stock-low');
                                }
                                
                                row.innerHTML = `
                                    <td>${escapeHTML(product.nombre_producto)}</td>
                                    <td class="text-center ${parseInt(product.cantidad) <= 10 ? 'text-danger fw-bold' : ''}">${escapeHTML(product.cantidad)}</td>
                                `;
                            });
                            productsCount.textContent = `${products.length} producto${products.length !== 1 ? 's' : ''}`;
                        }

                    } catch (error) {
                        console.error("Fetch supplier products error:", error);
                        // 🗑️ CAMBIO: colspan a 2
                        productsTbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger py-4">Error al cargar los productos.</td></tr>';
                        productsCount.textContent = 'Error';
                    } finally {
                        productsLoading.style.display = 'none';
                    }

                    // Mostrar el modal
                    const productsModal = new bootstrap.Modal(document.getElementById('productsModal'));
                    productsModal.show();
                }
            });

            function renderPagination(totalItems, itemsPerPage, currentPage, searchTerm) {
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                const paginationControls = document.getElementById('pagination-controls');
                paginationControls.innerHTML = '';

                if (totalPages <= 1) return;

                const createPageButton = (text, page, isDisabled = false, isActive = false) => {
                    const button = document.createElement('button');
                    button.className = `btn btn-sm ${isActive ? 'btn-primary' : 'btn-outline-primary'} ${isDisabled ? 'disabled' : ''}`;
                    button.textContent = text;
                    button.disabled = isDisabled;
                    
                    if (!isDisabled) {
                        button.addEventListener('click', (e) => {
                            e.preventDefault();
                            fetchSuppliers(page, searchTerm);
                        });
                    }
                    return button;
                };

                // Botón Anterior
                if (currentPage > 1) {
                    paginationControls.appendChild(createPageButton('Anterior', currentPage - 1));
                }

                // Números de página
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, currentPage + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationControls.appendChild(createPageButton(i, i, false, i === currentPage));
                }

                // Botón Siguiente
                if (currentPage < totalPages) {
                    paginationControls.appendChild(createPageButton('Siguiente', currentPage + 1));
                }
            }
        });
    </script>
</body>
</html>