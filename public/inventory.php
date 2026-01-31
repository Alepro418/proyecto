<?php
    session_start();   
?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Inventario</title>
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
        .table th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
        .loading-indicator {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .stock-low {
            background-color: #ffebee;
        }
        .pagination-controls {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 15px;
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
                        <a href="inventory.php" class="nav-link active">
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
            <header class="pb-3 mb-4 border-bottom">
                <h1 class="h2">Gestión de Inventario</h1>
            </header>

            <main class="flex-grow-1">
                <!-- Contenedor para las alarmas de bajo stock -->
                <div id="low-stock-notifications" class="mb-4">
                    <!-- Las notificaciones se cargarán aquí dinámicamente -->
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="input-group" style="width: 40%;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" id="search-input" class="form-control" placeholder="Buscar por nombre del producto.">
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2" id="total-products">0 productos</span>
                            <div class="spinner-border spinner-border-sm text-primary ms-2" id="loading-indicator" style="display: none;" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="loading-indicator" id="table-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando productos...</span>
                            </div>
                            <p class="mt-2">Cargando inventario...</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Proveedor</th>
                                        <th>Stock</th>
                                        <th>Stock Mínimo</th>
                                        <th>Precio Entrada</th>
                                        <th>Precio Salida</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Ubicación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-table-body">
                                    <!-- Los productos se cargarán aquí dinámicamente -->
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

    <!-- Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="restockModalLabel">Reabastecer Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="restockForm">
              <input type="hidden" id="productId" name="productId">
              <div class="mb-3">
                <label for="productName" class="form-label">Producto</label>
                <input type="text" class="form-control" id="productName" disabled>
              </div>
              <div class="mb-3">
                <label for="quantity" class="form-label">Cantidad</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
              </div>
              <div class="mb-3">
                <label for="price" class="form-label">Precio</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required min="0.01">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" form="restockForm" class="btn btn-primary">Reabastecer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const tableBody = document.getElementById('inventory-table-body');
            const paginationControls = document.getElementById('pagination-controls');
            const loadingIndicator = document.getElementById('loading-indicator');
            const tableLoading = document.getElementById('table-loading');
            const totalProductsSpan = document.getElementById('total-products');
            
            let currentPage = 1;
            const itemsPerPage = 15;

            function fetchProducts(page = 1, searchTerm = '') {
                // Mostrar indicadores de carga
                loadingIndicator.style.display = 'inline-block';
                tableLoading.style.display = 'block';
                tableBody.innerHTML = '';
                
                // Construimos la URL con los parámetros de paginación y búsqueda
                const url = `../api/get_products_data.php?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(searchTerm)}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = ''; // Limpiar tabla
                        if (data.products && data.products.length > 0) {
                            data.products.forEach(product => {
                                const row = document.createElement('tr');
                                
                                // Verificar si existe stock mínimo, si no, usar 0 como valor por defecto
                                const stockMinimo = product.Stock_minimo || 0;
                                const cantidad = product.Cantidad || 0;
                                
                                // Aplicar estilo de stock bajo
                                const stockClass = parseInt(cantidad) <= parseInt(stockMinimo) ? 'text-danger fw-bold' : '';
                                
                                // Aplicar fondo rojo claro si el stock es bajo
                                if (parseInt(cantidad) <= parseInt(stockMinimo)) {
                                    row.classList.add('stock-low');
                                }

                                row.innerHTML = `
                                    <td>${product.ID_Producto}</td>
                                    <td>${product.Codigo}</td>
                                    <td>${product.Nombre_Producto}</td>
                                    <td>${product.ID_Proveedor || 'N/A'}</td>
                                    <td class="${stockClass}">${cantidad}</td>
                                    <td class="${stockClass}">${stockMinimo}</td>
                                    <td>Bs ${parseFloat(product.Precio_de_entrada || 0).toFixed(2)}</td>
                                    <td>Bs ${parseFloat(product.Precio_de_Salida || 0).toFixed(2)}</td>
                                    <td>${product.Fecha_de_Ingreso || 'N/A'}</td>
                                    <td>${product.Ubicacion || 'N/A'}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary restock-btn" data-bs-toggle="modal" data-bs-target="#restockModal" data-product-id="${product.ID_Producto}" data-product-name="${product.Nombre_Producto}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </td>
                                `;
                                tableBody.appendChild(row);
                            });
                            totalProductsSpan.textContent = `${data.totalProducts || 0} productos`;
                            renderPagination(data.totalProducts, itemsPerPage, page);
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="11" class="text-center py-4">No se encontraron productos</td></tr>';
                            paginationControls.innerHTML = '';
                            totalProductsSpan.textContent = '0 productos';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        tableBody.innerHTML = '<tr><td colspan="11" class="text-center py-4 text-danger">Error al cargar los productos</td></tr>';
                        paginationControls.innerHTML = '';
                        totalProductsSpan.textContent = 'Error al cargar';
                    })
                    .finally(() => {
                        // Ocultar indicadores de carga
                        loadingIndicator.style.display = 'none';
                        tableLoading.style.display = 'none';
                    });
            }

            function renderPagination(totalItems, limit, page) {
                paginationControls.innerHTML = '';
                const totalPages = Math.ceil(totalItems / limit);
                
                if (totalPages <= 1) return;

                // Botón Anterior
                if (page > 1) {
                    const prevButton = document.createElement('button');
                    prevButton.className = 'btn btn-outline-primary btn-sm';
                    prevButton.textContent = 'Anterior';
                    prevButton.addEventListener('click', () => {
                        currentPage = page - 1;
                        fetchProducts(currentPage, searchInput.value);
                    });
                    paginationControls.appendChild(prevButton);
                }

                // Números de página
                const startPage = Math.max(1, page - 2);
                const endPage = Math.min(totalPages, page + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = `btn btn-sm ${i === page ? 'btn-primary' : 'btn-outline-primary'}`;
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => {
                        currentPage = i;
                        fetchProducts(currentPage, searchInput.value);
                    });
                    paginationControls.appendChild(pageButton);
                }

                // Botón Siguiente
                if (page < totalPages) {
                    const nextButton = document.createElement('button');
                    nextButton.className = 'btn btn-outline-primary btn-sm';
                    nextButton.textContent = 'Siguiente';
                    nextButton.addEventListener('click', () => {
                        currentPage = page + 1;
                        fetchProducts(currentPage, searchInput.value);
                    });
                    paginationControls.appendChild(nextButton);
                }
            }

            searchInput.addEventListener('input', () => {
                currentPage = 1; // Resetear a la primera página en cada búsqueda
                fetchProducts(currentPage, searchInput.value);
            });

            // Carga inicial de productos
            fetchProducts(currentPage);

            // --- Lógica para el Modal de Reabastecimiento ---
            const restockModal = document.getElementById('restockModal');
            if (restockModal) {
                restockModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const productId = button.getAttribute('data-product-id');
                    const productName = button.getAttribute('data-product-name');

                    const modalProductId = restockModal.querySelector('#productId');
                    const modalProductName = restockModal.querySelector('#productName');
                    
                    modalProductId.value = productId;
                    modalProductName.value = productName;
                    
                    // Limpiar campos del formulario
                    document.getElementById('quantity').value = '';
                    document.getElementById('price').value = '';
                });

                const restockForm = document.getElementById('restockForm');
                restockForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const productId = document.getElementById('productId').value;
                    const quantity = document.getElementById('quantity').value;
                    const price = document.getElementById('price').value;

                    // Validaciones básicas
                    if (!quantity || quantity <= 0) {
                        alert('Por favor ingrese una cantidad válida');
                        return;
                    }
                    
                    if (!price || price <= 0) {
                        alert('Por favor ingrese un precio válido');
                        return;
                    }

                    // Usaremos el endpoint 'restock_product.php' y enviaremos JSON
                    fetch('../api/restock_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity,
                            price: price
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(restockModal);
                            modal.hide();
                            // Resetear el formulario
                            restockForm.reset();
                            fetchProducts(currentPage, searchInput.value); // Recargar la tabla
                            alert('Producto reabastecido exitosamente');
                        } else {
                            alert('Error al reabastecer: ' + (data.error || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error en el formulario de reabastecimiento:', error);
                        alert('Error al conectar con el servidor');
                    });
                });
            }
        });
    </script>
</body>
</html>