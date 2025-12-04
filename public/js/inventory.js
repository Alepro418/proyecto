document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const tableBody = document.getElementById('inventory-table-body');
    const paginationControls = document.getElementById('pagination-controls');
    let currentPage = 1;
    const itemsPerPage = 15;

    function fetchProducts(page = 1, searchTerm = '') {
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
                    renderPagination(data.totalProducts, itemsPerPage, page);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="11" class="text-center">No se encontraron productos</td></tr>';
                    paginationControls.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                tableBody.innerHTML = '<tr><td colspan="11" class="text-center">Error al cargar los productos</td></tr>';
            });
    }

    function renderPagination(totalItems, limit, page) {
        paginationControls.innerHTML = '';
        const totalPages = Math.ceil(totalItems / limit);
        
        if (totalPages <= 1) return;

        // Botón Anterior
        if (page > 1) {
            const prevButton = document.createElement('button');
            prevButton.className = 'btn btn-outline-primary btn-sm me-1';
            prevButton.textContent = 'Anterior';
            prevButton.addEventListener('click', () => {
                currentPage = page - 1;
                fetchProducts(currentPage, searchInput.value);
            });
            paginationControls.appendChild(prevButton);
        }

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.className = `btn btn-sm me-1 ${i === page ? 'btn-primary' : 'btn-outline-primary'}`;
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
            nextButton.className = 'btn btn-outline-primary btn-sm ms-1';
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