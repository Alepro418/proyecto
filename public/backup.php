<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: sign_in.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praxis - Copias de Seguridad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; padding-top: 20px; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: #fff; }
        .sidebar .nav-link.active { color: #fff; font-weight: bold; }
        .backup-card { cursor: pointer; transition: transform 0.2s; }
        .backup-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar (COPIA EXACTA de tus otros archivos) -->
        <div class="sidebar-fixed d-none d-md-flex bg-dark">
            <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar-content" style="width: 100%; min-height: 100vh;">
                <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="assets/logo.jpg" alt="Logo" width="40" height="40" class="rounded-circle me-2">
                    <span class="fs-4">Praxis</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link text-white">
                            <i class="bi bi-house-door-fill me-2"></i> Inicio
                        </a>
                    </li>
                    <li><a href="inventory.php" class="nav-link text-white"><i class="bi bi-box-seam me-2"></i> Inventario</a></li>
                    <li><a href="suppliers.php" class="nav-link text-white"><i class="bi bi-truck me-2"></i> Proveedores</a></li>
                    <li><a href="sales.php" class="nav-link text-white"><i class="bi bi-cart-check-fill me-2"></i> Ventas</a></li>
                    <li><a href="alarms.php" class="nav-link text-white"><i class="bi bi-bell-fill me-2"></i> Alarmas</a></li>
                    <li><a href="reports.php" class="nav-link text-white"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Reportes</a></li>
                    <li><a href="shopping.php" class="nav-link text-white"><i class="bi bi-bag-plus-fill me-2"></i> Compras</a></li>
                    <li><a href="add_product.php" class="nav-link text-white"><i class="bi bi-plus-circle-fill me-2"></i> Agregar Producto</a></li>
                    <li><a href="reg_sale.php" class="nav-link text-white"><i class="bi bi-journal-plus me-2"></i> Registrar Venta</a></li>
                    <li>
                        <a href="backup.php" class="nav-link active">
                            <i class="bi bi-database-fill me-2"></i> Copias de Seguridad
                        </a>
                    </li>
                    <li><a href="about.php" class="nav-link text-white"><i class="bi bi-info-circle-fill me-2"></i> Acerca de</a></li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/user_icon.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado'; ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="../src/auth/logout.php">Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto p-4 d-flex flex-column">
            <header class="pb-3 mb-4 border-bottom">
                <h1 class="h2">Sistema de Copias de Seguridad</h1>
                <p class="lead text-muted">Protege tus datos con copias de seguridad automáticas y manuales</p>
            </header>

            <main class="flex-grow-1">
                <div class="row g-4">
                    <!-- Copia Manual -->
                    <div class="col-md-6">
                        <div class="card backup-card h-100" onclick="createBackup(event)">
                            <div class="card-body text-center">
                                <i class="bi bi-database-fill-down display-1 text-primary mb-3"></i>
                                <h4 class="card-title">Crear Copia Manual</h4>
                                <p class="card-text">Genera una copia de seguridad inmediata de toda la base de datos.</p>
                                <button class="btn btn-primary">
                                    <i class="bi bi-download"></i> Descargar Copia Ahora
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurar -->
                    <div class="col-md-6">
                        <div class="card backup-card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-database-fill-up display-1 text-warning mb-3"></i>
                                <h4 class="card-title">Restaurar Base de Datos</h4>
                                <p class="card-text">Restaura la base de datos desde un archivo de copia de seguridad.</p>
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" id="backupFile" accept=".sql">
                                    <button class="btn btn-warning" onclick="restoreBackup(event)">
                                        <i class="bi bi-upload"></i> Restaurar
                                    </button>
                                </div>
                                <small class="text-muted">Solo archivos .sql de hasta 10MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información de Copias</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-check text-primary fs-3 me-3"></i>
                                            <div>
                                                <h6>Última Copia</h6>
                                                <p class="mb-0" id="last-backup"><?php echo date('d/m/Y H:i'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-database text-success fs-3 me-3"></i>
                                            <div>
                                                <h6>Tamaño Base de Datos</h6>
                                                <p class="mb-0">~ 250 KB</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-check text-danger fs-3 me-3"></i>
                                            <div>
                                                <h6>Recomendación</h6>
                                                <p class="mb-0">Realizar copias semanales</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Copias</h5>
                                <span class="badge bg-primary" id="backup-count">0 copias</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Tamaño</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="backup-history">
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando historial de copias...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
    // ---------- FUNCIONES REALES (REEMPLAZAN LAS SIMULADAS) ----------
    
    // Función para crear copia de seguridad REAL
    async function createBackup(event) {
        if (!confirm('¿Estás seguro de querer crear una copia de seguridad?')) {
            return;
        }
        
        const backupBtn = event.target.closest('.backup-card').querySelector('button');
        const originalText = backupBtn.innerHTML;
        backupBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generando...';
        backupBtn.disabled = true;
        
        try {
            const response = await fetch('../src/core/create_backup.php', {
                method: 'POST',
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                // Si la respuesta es JSON con error
                if (response.headers.get('content-type')?.includes('application/json')) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error en el servidor');
                }
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            
            // Obtener nombre del archivo del header o crear uno
            const contentDisposition = response.headers.get('content-disposition');
            let filename = `backup_praxis_${new Date().toISOString().slice(0,10)}.sql`;
            
            if (contentDisposition) {
                const matches = /filename="(.+?)"/.exec(contentDisposition);
                if (matches && matches[1]) {
                    filename = matches[1];
                }
            }
            
            // Crear y descargar archivo
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            // Actualizar UI
            alert('✅ Copia de seguridad creada exitosamente');
            document.getElementById('last-backup').textContent = new Date().toLocaleString('es-VE');
            
            // Agregar al historial (versión simplificada)
            addToHistory(filename, blob.size);
            
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error al crear la copia de seguridad: ' + error.message);
        } finally {
            backupBtn.innerHTML = originalText;
            backupBtn.disabled = false;
        }
    }

    // Función para restaurar copia REAL
    async function restoreBackup(event) {
        const fileInput = document.getElementById('backupFile');
        const file = fileInput.files[0];
        
        // Validaciones del lado del cliente
        if (!file) {
            alert('⚠️ Por favor selecciona un archivo .sql primero');
            return;
        }
        
        // Mejor validación de extensión
        const validExtensions = ['.sql', '.SQL'];
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(fileExt)) {
            alert('❌ Solo se permiten archivos .sql');
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            alert('❌ El archivo es demasiado grande (máximo 10MB)');
            return;
        }
        
        if (!confirm('⚠️ ADVERTENCIA CRÍTICA:\n\n• Esto sobrescribirá TODA la base de datos actual\n• La operación NO se puede deshacer\n• Asegúrate de tener una copia reciente\n\n¿Estás absolutamente seguro?')) {
            return;
        }
        
        const restoreBtn = event.target;
        const originalText = restoreBtn.innerHTML;
        restoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Restaurando...';
        restoreBtn.disabled = true;
        
        try {
            const formData = new FormData();
            formData.append('backup_file', file);
            
            const response = await fetch('../src/core/restore_backup.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (!response.ok || result.error) {
                throw new Error(result.message || 'Error en la restauración');
            }
            
            alert('✅ ' + result.message + '\nLa página se recargará en 3 segundos...');
            
            // Recargar la página para reflejar cambios en la BD
            setTimeout(() => {
                location.reload();
            }, 3000);
            
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error al restaurar: ' + error.message);
        } finally {
            restoreBtn.innerHTML = originalText;
            restoreBtn.disabled = false;
            fileInput.value = '';
        }
    }

    // ---------- FUNCIONES AUXILIARES MEJORADAS ----------
    
    // Agregar entrada al historial
    function addToHistory(filename, size) {
        const historyRow = `
            <tr>
                <td>${new Date().toLocaleString('es-VE')}</td>
                <td><span class="badge bg-primary">Manual</span></td>
                <td>${formatFileSize(size)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="simulateDownload('${filename}')">
                        <i class="bi bi-download"></i> Descargar
                    </button>
                </td>
            </tr>
        `;
        
        const tbody = document.getElementById('backup-history');
        if (tbody.innerHTML.includes('Cargando')) {
            tbody.innerHTML = historyRow;
        } else {
            tbody.innerHTML = historyRow + tbody.innerHTML;
        }
        
        updateBackupCount();
    }
    
    // Formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
    
    // Simular descarga (para historial - en realidad necesitarías archivos guardados)
    function simulateDownload(filename) {
        alert(`⏳ Función en desarrollo\n\nEn una versión real, se descargaría: ${filename}\n\nPor ahora, usa "Crear Copia Manual" para generar un nuevo backup.`);
    }

    // Cargar historial inicial
    function loadBackupHistory() {
        setTimeout(() => {
            // Datos de ejemplo
            const history = [
                { date: '02/12/2025 12:00', type: 'Manual', size: '256 KB' },
                { date: '25/11/2025 10:30', type: 'Manual', size: '245 KB' },
                { date: '18/11/2025 09:15', type: 'Manual', size: '240 KB' }
            ];
            
            let html = '';
            history.forEach(backup => {
                html += `
                    <tr>
                        <td>${backup.date}</td>
                        <td><span class="badge bg-primary">${backup.type}</span></td>
                        <td>${backup.size}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="simulateDownload('backup_${backup.date.replace(/[/:]/g, '-')}.sql')">
                                <i class="bi bi-download"></i> Descargar
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            document.getElementById('backup-history').innerHTML = html;
            updateBackupCount();
        }, 1000);
    }

    function updateBackupCount() {
        const count = document.getElementById('backup-history').querySelectorAll('tr').length;
        document.getElementById('backup-count').textContent = `${count} copias`;
    }

    // Cargar al iniciar
    document.addEventListener('DOMContentLoaded', loadBackupHistory);
    
    // Pequeña mejora: Prevenir envío accidental del formulario
    document.querySelector('input[type="file"]')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && !file.name.toLowerCase().endsWith('.sql')) {
            alert('Solo se permiten archivos .sql');
            e.target.value = '';
        }
    });
</script>
</body>
</html>