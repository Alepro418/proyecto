<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Reportes</title>
    <link rel="shortcut-icon" href="assets/logo.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        
        .report-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #dee2e6;
        }
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .report-card.active {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .stock-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .bg-out-of-stock { background-color: #dc3545; color: white; }
        .bg-low-stock { background-color: #ffc107; color: black; }
        .bg-normal-stock { background-color: #28a745; color: white; }
        
        /* Loading animation */
        .lds-ring {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .lds-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #0d6efd;
            border-radius: 50%;
            animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #0d6efd transparent transparent transparent;
        }
        .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
        .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
        .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
        @keyframes lds-ring {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar-fixed d-none d-md-flex bg-dark" style="min-height: 100vh; border-right: 1px solid #dee2e6;">
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
                        <a href="reports.php" class="nav-link active">
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

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto p-4 d-flex flex-column">
            <header class="pb-3 mb-4 border-bottom">
                <h1 class="h2">Sistema de Reportes</h1>
                <p class="lead text-muted">Genera reportes detallados de ventas, inventario y productos más vendidos</p>
            </header>

            <main class="flex-grow-1">
                <!-- Selección de Tipo de Reporte -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Seleccionar Tipo de Reporte</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="report-card text-center p-4 border rounded" data-report-type="sales">
                                    <i class="bi bi-currency-dollar display-4 text-primary mb-3"></i>
                                    <h5>Reporte de Ventas</h5>
                                    <p class="text-muted small">Ventas por día, semana o mes</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="report-card text-center p-4 border rounded" data-report-type="inventory">
                                    <i class="bi bi-box-seam display-4 text-warning mb-3"></i>
                                    <h5>Reporte de Inventario</h5>
                                    <p class="text-muted small">Productos agotados o con stock bajo</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="report-card text-center p-4 border rounded" data-report-type="top-products">
                                    <i class="bi bi-trophy display-4 text-success mb-3"></i>
                                    <h5>Productos Más Vendidos</h5>
                                    <p class="text-muted small">Top productos por mes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros (se muestran dinámicamente) -->
                <div class="card mb-4" id="filters-section" style="display: none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="report-title">Configurar Reporte</h5>
                        <div id="report-stats"></div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros para Ventas -->
                        <div id="sales-filters" class="filters-section" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="sales-period" class="form-label">Periodo</label>
                                    <select id="sales-period" class="form-select">
                                        <option value="daily">Diario</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly" selected>Mensual</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="sales-date" class="form-label">Fecha</label>
                                    <input type="date" id="sales-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="sales-group" class="form-label">Mostrar</label>
                                    <select id="sales-group" class="form-select">
                                        <option value="product">Por Producto</option>
                                        <option value="date">Por Fecha</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Filtros para Inventario -->
                        <div id="inventory-filters" class="filters-section" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="inventory-type" class="form-label">Tipo de Reporte</label>
                                    <select id="inventory-type" class="form-select">
                                        <option value="all">Todo el Inventario</option>
                                        <option value="low-stock" selected>Productos con Stock Bajo</option>
                                        <option value="out-of-stock">Productos Agotados</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inventory-sort" class="form-label">Ordenar por</label>
                                    <select id="inventory-sort" class="form-select">
                                        <option value="stock">Stock (menor a mayor)</option>
                                        <option value="name">Nombre (A-Z)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Filtros para Top Productos -->
                        <div id="top-products-filters" class="filters-section" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="top-month" class="form-label">Mes</label>
                                    <input type="month" id="top-month" class="form-control" value="<?php echo date('Y-m'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="top-limit" class="form-label">Cantidad</label>
                                    <select id="top-limit" class="form-select">
                                        <option value="5">Top 5</option>
                                        <option value="10" selected>Top 10</option>
                                        <option value="15">Top 15</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="top-metric" class="form-label">Métrica</label>
                                    <select id="top-metric" class="form-select">
                                        <option value="quantity">Cantidad Vendida</option>
                                        <option value="revenue">Ingresos</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button id="generate-report" class="btn btn-primary">
                                <i class="bi bi-play-fill"></i> Generar Reporte
                            </button>
                            <button id="export-report" class="btn btn-outline-secondary ms-2" style="display: none;">
                                <i class="bi bi-download"></i> Exportar a CSV
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="card" id="results-section" style="display: none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="results-title">Resultados del Reporte</h5>
                        <div id="results-stats"></div>
                    </div>
                    <div class="card-body">
                        <!-- Gráfico -->
                        <div class="mb-4">
                            <canvas id="report-chart" height="250"></canvas>
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="report-table">
                                <thead id="table-head" class="table-light"></thead>
                                <tbody id="table-body"></tbody>
                            </table>
                        </div>

                        <!-- Sin resultados -->
                        <div id="no-results" style="display: none;">
                            <div class="text-center py-5">
                                <i class="bi bi-clipboard-data display-1 text-muted"></i>
                                <h4 class="mt-3">No hay datos para mostrar</h4>
                                <p class="text-muted">Prueba con diferentes filtros</p>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div id="report-loading" class="text-center py-5" style="display: none;">
                            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                            <p class="mt-3">Generando reporte...</p>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Sistema de reportes cargado');
            
            // Variables globales
            let currentReportType = null;
            let currentChart = null;
            let currentData = null;
            
            // Elementos DOM
            const reportCards = document.querySelectorAll('.report-card');
            const filtersSection = document.getElementById('filters-section');
            const resultsSection = document.getElementById('results-section');
            const generateBtn = document.getElementById('generate-report');
            const exportBtn = document.getElementById('export-report');
            const reportLoading = document.getElementById('report-loading');
            const noResults = document.getElementById('no-results');
            const tableBody = document.getElementById('table-body');
            const tableHead = document.getElementById('table-head');
            const reportChart = document.getElementById('report-chart');
            const reportStats = document.getElementById('report-stats');
            const resultsStats = document.getElementById('results-stats');
            
            // Configurar fechas por defecto
            document.getElementById('sales-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('top-month').value = new Date().toISOString().slice(0, 7);
            
            // 1. Selección de tipo de reporte
            reportCards.forEach(card => {
                card.addEventListener('click', function() {
                    console.log('📊 Reporte seleccionado:', this.dataset.reportType);
                    
                    // Remover activo de todas
                    reportCards.forEach(c => c.classList.remove('active'));
                    
                    // Activar la seleccionada
                    this.classList.add('active');
                    
                    // Ocultar todos los filtros
                    document.querySelectorAll('.filters-section').forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    // Establecer tipo actual
                    currentReportType = this.dataset.reportType;
                    
                    // Mostrar filtros correspondientes
                    document.getElementById(currentReportType + '-filters').style.display = 'block';
                    
                    // Mostrar sección de filtros
                    filtersSection.style.display = 'block';
                    resultsSection.style.display = 'none';
                    exportBtn.style.display = 'none';
                    
                    // Actualizar títulos
                    updateTitles();
                });
            });
            
            function updateTitles() {
                const titles = {
                    'sales': 'Reporte de Ventas',
                    'inventory': 'Reporte de Inventario',
                    'top-products': 'Productos Más Vendidos'
                };
                
                if (currentReportType) {
                    document.getElementById('report-title').textContent = titles[currentReportType];
                    document.getElementById('results-title').textContent = titles[currentReportType];
                }
            }
            
            // 2. Generar reporte
            generateBtn.addEventListener('click', function() {
                if (!currentReportType) {
                    alert('⚠️ Por favor selecciona un tipo de reporte primero');
                    return;
                }
                
                console.log('🚀 Generando reporte:', currentReportType);
                generateReport();
            });
            
            async function generateReport() {
                try {
                    // Mostrar loading
                    reportLoading.style.display = 'block';
                    noResults.style.display = 'none';
                    tableBody.innerHTML = '';
                    tableHead.innerHTML = '';
                    resultsStats.innerHTML = '';
                    
                    // Ocultar resultados anteriores
                    resultsSection.style.display = 'none';
                    
                    // Destruir gráfico anterior
                    if (currentChart) {
                        currentChart.destroy();
                    }
                    
                    // Construir URL de la API
                    let url = `../api/get_report.php?type=${currentReportType}`;
                    
                    // Agregar parámetros según tipo
                    switch(currentReportType) {
                        case 'sales':
                            url += `&period=${document.getElementById('sales-period').value}`;
                            url += `&date=${document.getElementById('sales-date').value}`;
                            break;
                        case 'inventory':
                            url += `&report_type=${document.getElementById('inventory-type').value}`;
                            break;
                        case 'top-products':
                            url += `&month=${document.getElementById('top-month').value}`;
                            url += `&limit=${document.getElementById('top-limit').value}`;
                            url += `&metric=${document.getElementById('top-metric').value}`;
                            break;
                    }
                    
                    console.log('🔗 URL de la API:', url);
                    
                    // Llamar a la API
                    const response = await fetch(url);
                    
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('📦 Datos recibidos:', data);
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Error desconocido');
                    }
                    
                    // Guardar datos
                    currentData = data;
                    
                    // Mostrar resultados
                    displayResults(data);
                    
                } catch (error) {
                    console.error('❌ Error:', error);
                    showError(error.message);
                } finally {
                    reportLoading.style.display = 'none';
                }
            }
            
            function displayResults(data) {
                console.log('🎯 Mostrando resultados');
                
                // Mostrar sección de resultados
                resultsSection.style.display = 'block';
                exportBtn.style.display = 'inline-block';
                
                // Mostrar estadísticas
                if (data.stats) {
                    displayStats(data.stats);
                }
                
                // Mostrar tabla
                if (data.data && data.data.length > 0) {
                    renderTable(data.data, data.columns);
                    noResults.style.display = 'none';
                } else {
                    noResults.style.display = 'block';
                }
                
                // Mostrar gráfico
                if (data.chart && data.chart.labels && data.chart.data) {
                    renderChart(data.chart);
                }
            }
            
            function displayStats(stats) {
                let statsHTML = '';
                
                if (stats.total_ventas) {
                    statsHTML += `<span class="badge bg-primary me-2">Total: Bs ${stats.total_ventas}</span>`;
                }
                
                if (stats.total_productos) {
                    statsHTML += `<span class="badge bg-secondary me-2">${stats.total_productos} productos</span>`;
                }
                
                if (stats.agotados !== undefined) {
                    statsHTML += `<span class="badge bg-danger me-2">${stats.agotados} agotados</span>`;
                }
                
                if (stats.bajos !== undefined) {
                    statsHTML += `<span class="badge bg-warning me-2">${stats.bajos} bajos</span>`;
                }
                
                if (stats.total_cantidad) {
                    statsHTML += `<span class="badge bg-success me-2">${stats.total_cantidad} unidades</span>`;
                }
                
                resultsStats.innerHTML = statsHTML;
            }
            
            function renderTable(data, columns) {
                // Limpiar tabla
                tableHead.innerHTML = '';
                tableBody.innerHTML = '';
                
                // Crear encabezado
                const headerRow = document.createElement('tr');
                
                columns.forEach(column => {
                    const th = document.createElement('th');
                    th.textContent = column;
                    headerRow.appendChild(th);
                });
                
                tableHead.appendChild(headerRow);
                
                // Crear filas de datos
                data.forEach(item => {
                    const row = document.createElement('tr');
                    
                    // Usar las claves del primer objeto de datos para asegurar el orden correcto
                    const keys = Object.keys(item);

                    columns.forEach(columnName => {
                        const td = document.createElement('td');
                        // Mapear el nombre de la columna a la clave del objeto de datos
                        const keyMap = {
                            'Fecha': 'fecha',
                            'Producto': 'producto',
                            'Cantidad': 'cantidad',
                            'Cantidad Vendida': 'cantidad_vendida',
                            'Total (Bs)': 'total',
                            'Stock Actual': 'stock_actual',
                            'Stock Mínimo': 'stock_minimo',
                            'Precio (Bs)': 'precio',
                            'Estado': 'estado',
                            'Ingresos (Bs)': 'ingresos',
                            'Porcentaje': 'porcentaje'
                        };
                        const key = Object.keys(keyMap).find(k => keyMap[k] === columnName.toLowerCase().replace(' (bs)', '').replace(' ', '_')) || columnName.toLowerCase();
                        let value = item[key] || item[keyMap[columnName]] || '';
                        
                        // Formatear valores específicos
                        if (['Total (Bs)', 'Precio (Bs)', 'Ingresos (Bs)'].includes(columnName)) {
                            value = `Bs ${parseFloat(value || 0).toFixed(2)}`;
                        } else if (columnName === 'Porcentaje') {
                            value = `${parseFloat(value || 0).toFixed(1)}%`;
                        } else if (columnName === 'Estado') {
                            if (value === 'Agotado') td.classList.add('text-danger', 'fw-bold');
                            if (value === 'Bajo') td.classList.add('text-warning', 'fw-bold');
                        }
                        
                        td.textContent = value;
                        row.appendChild(td);
                    });

                    tableBody.appendChild(row);
                });
            }
            
            function renderChart(chartData) {
                const ctx = reportChart.getContext('2d');
                
                // Determinar tipo de gráfico
                let chartType = 'bar';
                if (currentReportType === 'inventory') { // El de inventario será tipo 'pie'
                    chartType = 'pie';
                }
                
                currentChart = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: chartData.label,
                            data: chartData.data,
                            backgroundColor: getChartColors(chartData.data.length),
                            borderColor: getChartColors(chartData.data.length).map(c => c.replace('0.7', '1')),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: chartData.label
                            }
                        }
                    }
                });
            }
            
            function getChartColors(count) {
                if (currentReportType === 'inventory') {
                    return ['#dc3545', '#ffc107', '#17a2b8', '#28a745'];
                }
                
                // Colores para gráficos de barras
                const colors = [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(201, 203, 207, 0.7)'
                ];
                
                return colors.slice(0, count);
            }
            
            function showError(message) {
                noResults.style.display = 'block';
                noResults.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                        <h4 class="mt-3">Error al generar reporte</h4>
                        <p class="text-danger">${message}</p>
                        <button class="btn btn-primary mt-3" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Reintentar
                        </button>
                    </div>
                `;
            }
            
            // 3. Exportar a CSV
            exportBtn.addEventListener('click', function() {
                if (!currentData || !currentData.data) {
                    alert('No hay datos para exportar');
                    return;
                }
                
                exportToCSV(currentData.data, currentData.columns);
            });
            
            function exportToCSV(data, columns) {
                // Crear contenido CSV
                let csvContent = columns.join(',') + '\n';
                
                data.forEach(item => {
                    const row = columns.map(column => {
                        let value = '';
                        
                        switch(column) {
                            case 'Fecha':
                                value = item.fecha || '';
                                break;
                            case 'Producto':
                                value = item.producto || item.nombre_producto || '';
                                break;
                            case 'Cantidad':
                            case 'Cantidad Vendida':
                                value = item.cantidad || item.cantidad_vendida || 0;
                                break;
                            case 'Total (Bs)':
                                value = item.total || 0;
                                break;
                            case 'Stock Actual':
                                value = item.stock_actual || item.cantidad || 0;
                                break;
                            case 'Stock Mínimo':
                                value = item.stock_minimo || 0;
                                break;
                            case 'Precio (Bs)':
                                value = item.precio || 0;
                                break;
                            case 'Estado':
                                value = item.estado || '';
                                break;
                            case 'Ingresos (Bs)':
                                value = item.ingresos || 0;
                                break;
                            case 'Porcentaje':
                                value = item.porcentaje || 0;
                                break;
                            default:
                                value = item[column.toLowerCase()] || '';
                        }
                        
                        // Escapar comas y comillas
                        return `"${String(value).replace(/"/g, '""')}"`;
                    }).join(',');
                    
                    csvContent += row + '\n';
                });
                
                // Descargar archivo
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `reporte_${currentReportType}_${new Date().toISOString().slice(0,10)}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                console.log('📥 Reporte exportado');
            }
        });
    </script>
</body>
</html>