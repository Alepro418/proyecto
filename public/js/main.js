
    let currentPage = 1;
    const productsPerPage = 15; // Número de productos a mostrar por página

    document.addEventListener('DOMContentLoaded', function() {
        fetchProducts(currentPage);
    });

    async function fetchProducts(page) {
        const tbody = document.querySelector('.table-container table tbody');
        const paginationControls = document.getElementById('pagination-controls');
        tbody.innerHTML = ''; // Limpiar cualquier fila existente
        paginationControls.innerHTML = ''; // Limpiar controles de paginación existentes

        try {
            const response = await fetch(`../api/get_products_data.php?page=${page}&limit=${productsPerPage}`);

            if (!response.ok) {
                throw new Error(`Error HTTP! Estado: ${response.status}`);
            }

            const data = await response.json();

            if (data.error) {
                console.error("Error del servidor:", data.error);
                tbody.innerHTML = `<tr><td colspan="6">Error al cargar los datos: ${data.error}</td></tr>`;
                return;
            }

            const products = data.products;
            const totalProducts = data.totalProducts;
            const limit = data.limit;
            
            // Llama a la nueva función para verificar y mostrar las alertas
            checkLowStockAlerts(products);

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No hay productos registrados.</td></tr>';
                return;
            }

            // Recorrer el array de productos y añadir cada uno como una fila
products.forEach(product => {
    const row = tbody.insertRow();
    row.insertCell().textContent = product.Producto;
    row.insertCell().textContent = parseFloat(product.Precio_de_entrada).toFixed(2);
    row.insertCell().textContent = parseFloat(product.Precio_de_venta).toFixed(2);
    
    // Crear la celda de la cantidad
    const cantidadCell = row.insertCell();
    cantidadCell.textContent = product.Cantidad;
    
    // Si la cantidad es menor a 10, añade la clase 'low-stock'
    if (parseInt(product.Cantidad) < 10) {
        cantidadCell.classList.add('low-stock');
    }

    row.insertCell().textContent = product.Proveedor;
    row.insertCell().textContent = product.Fecha_ingreso;
});

            // Generar controles de paginación
            renderPagination(totalProducts, limit, page);

        } catch (error) {
            console.error("Error al obtener los productos:", error);
            tbody.innerHTML = '<tr><td colspan="6">Error al cargar los datos. Por favor, inténtalo de nuevo más tarde.</td></tr>';
        }
    }

    function renderPagination(totalItems, itemsPerPage, currentPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const paginationControls = document.getElementById('pagination-controls');
        paginationControls.innerHTML = ''; // Limpiar paginación anterior

        // Botón "Anterior"
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Anterior';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchProducts(currentPage);
            }
        });
        paginationControls.appendChild(prevButton);

        // Números de página
        const maxPagesToShow = 5; // Limita el número de botones de página mostrados
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            if (i === currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', () => {
                currentPage = i;
                fetchProducts(currentPage);
            });
            paginationControls.appendChild(pageButton);
        }

        // Botón "Siguiente"
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Siguiente';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                fetchProducts(currentPage);
            }
        });
        paginationControls.appendChild(nextButton);
    }
    
/**
* Revisa los productos con bajos niveles de existencias y muestra notificaciones.
* @param {Array} products - La lista de productos a revisar.
*/

function checkLowStockAlerts(products) {
    const notificationsContainer = document.getElementById('low-stock-notifications');
    notificationsContainer.innerHTML = '';

    const lowStockProducts = products.filter(product => product.low_stock);
        
    if (lowStockProducts.length > 0) {
        lowStockProducts.forEach(product => {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <strong>¡ALERTA!</strong> El producto "${product.Producto}" tiene niveles bajos de existencias. Cantidad: ${product.Cantidad}
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            `;
            notificationsContainer.appendChild(notification);
        });
    }
}

let currentPage = 1;
const suppliersPerPage = 15; // Número de proveedores a mostrar por página

document.addEventListener('DOMContentLoaded', function() {
    fetchSuppliers(currentPage); // Cambia la llamada a la nueva función
});

async function fetchSuppliers(page) { // Renombra la función
    const tbody = document.querySelector('.inventory table tbody'); // O .suppliers-table table tbody si la renombraste
    const paginationControls = document.getElementById('pagination-controls');
    tbody.innerHTML = '';
    paginationControls.innerHTML = '';

    try {
        // *** CAMBIO CLAVE AQUÍ: APUNTAR A UN NUEVO ARCHIVO PHP PARA PROVEEDORES ***
        const response = await fetch(`get_suppliers_data.php?page=${page}&limit=${suppliersPerPage}`);

        if (!response.ok) {
        throw new Error(`Error HTTP! Estado: ${response.status}`);
        }

        const data = await response.json();

        if (data.error) {
            console.error("Error del servidor:", data.error);
            tbody.innerHTML = `<tr><td colspan="6">Error al cargar los datos de proveedores: ${data.error}</td></tr>`;
            return;
        }

        const suppliers = data.suppliers; // *** CAMBIO CLAVE AQUÍ: ESPERAR 'suppliers' en lugar de 'products' ***
        const totalSuppliers = data.totalSuppliers; // Asume que el PHP devolverá 'totalSuppliers'
        const limit = data.limit;

        if (suppliers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No hay proveedores registrados.</td></tr>';
            return;
        }

        suppliers.forEach(supplier => {
            const row = tbody.insertRow();
            row.insertCell().textContent = supplier.Nombre_proveedor; // Coincide con la columna 'Proveedor' de tu DB
            row.insertCell().textContent = supplier.Rif;       // Coincide con la columna 'Rif' de tu DB
            row.insertCell().textContent = supplier.Telefono;   // Coincide con la columna 'Teléfono' de tu DB
            row.insertCell().textContent = supplier.Correo;     // Coincide con la columna 'Correo' de tu DB
            row.insertCell().textContent = supplier.Ciudad; // Coincide con la columna 'Contraseña' de tu DB
    // Las columnas 'direccion' y 'fecha_registro' no están siendo seleccionadas en tu PHP actual.
    // Si necesitas mostrarlas, debes añadirlas a tu consulta SELECT en get_suppliers_data.php
});
// ...

        // Generar controles de paginación
        renderPagination(totalSuppliers, limit, page); // Pasa totalSuppliers
    } catch (error) {
        console.error("Error al obtener los proveedores:", error);
        tbody.innerHTML = '<tr><td colspan="6">Error al cargar los datos de proveedores. Por favor, inténtalo de nuevo más tarde.</td></tr>';
    }
}

// La función renderPagination puede quedarse igual, ya que es genérica para la paginación.
function renderPagination(totalItems, itemsPerPage, currentPage) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationControls = document.getElementById('pagination-controls');
    paginationControls.innerHTML = '';

    const prevButton = document.createElement('button');
    prevButton.textContent = 'Anterior';
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchSuppliers(currentPage); // Asegúrate de llamar a fetchSuppliers aquí
        }
    });
    paginationControls.appendChild(prevButton);

    const maxPagesToShow = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

    if (endPage - startPage + 1 < maxPagesToShow) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        if (i === currentPage) {
            pageButton.classList.add('active');
        }
        pageButton.addEventListener('click', () => {
            currentPage = i;
            fetchSuppliers(currentPage); // Asegúrate de llamar a fetchSuppliers aquí
        });
        paginationControls.appendChild(pageButton);
    }

    const nextButton = document.createElement('button');
    nextButton.textContent = 'Siguiente';
    nextButton.disabled = currentPage === totalPages;
    nextButton.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            fetchSuppliers(currentPage); // Asegúrate de llamar a fetchSuppliers aquí
        }
    });
    paginationControls.appendChild(nextButton);
}

document.addEventListener('DOMContentLoaded', function() {
            fetchLowStockProducts();
        });

        async function fetchLowStockProducts() {
            const notificationsContainer = document.getElementById('low-stock-notifications');
            notificationsContainer.innerHTML = ''; // Limpiar notificaciones anteriores

            try {
                // Asume que tienes un endpoint que devuelve solo los productos con stock bajo
                const response = await fetch('get_low_stock_products.php');

                if (!response.ok) {
                    throw new Error(`Error HTTP! Estado: ${response.status}`);
                }

                const data = await response.json();

                if (data.error) {
                    notificationsContainer.innerHTML = `<p class="notification">Error: ${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    notificationsContainer.innerHTML = '<p class="no-alarms">No hay alarmas de stock en este momento.</p>';
                    return;
                }

                data.forEach(product => {
                    const notification = document.createElement('div');
                    notification.className = 'notification';
                    notification.innerHTML = `
                        <strong>¡ALERTA!</strong> El producto "${product.Producto}" tiene niveles bajos de existencias. Cantidad: ${product.Cantidad}
                    `;
                    notificationsContainer.appendChild(notification);
                });

            } catch (error) {
                console.error("Error al obtener las alarmas:", error);
                notificationsContainer.innerHTML = `<p class="notification">Error al cargar las alarmas. Por favor, inténtalo de nuevo más tarde.</p>`;
            }
        }