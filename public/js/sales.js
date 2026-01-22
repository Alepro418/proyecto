document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const salesPerPage = 15;

    // Modal Instance
    const detailsModalEl = document.getElementById('saleDetailsModal');
    const detailsModal = new bootstrap.Modal(detailsModalEl);

    // DOM Elements
    const searchInput = document.getElementById('search-input');
    const dateInput = document.getElementById('date-input');
    const paymentMethodInput = document.getElementById('payment-method-input');

    // Initial Load
    fetchSales(currentPage, '', '', '');

    // Search Event
    searchInput.addEventListener('input', () => {
        dateInput.value = ''; // Clear date input
        paymentMethodInput.value = ''; // Clear payment method input
        fetchSales(1, searchInput.value.trim(), '', '');
    });

    // Date search event
    dateInput.addEventListener('change', () => {
        searchInput.value = ''; // Clear search input
        fetchSales(1, '', dateInput.value, paymentMethodInput.value);
    });

    // Payment method search event
    paymentMethodInput.addEventListener('change', () => {
        searchInput.value = ''; // Clear search input
        fetchSales(1, '', dateInput.value, paymentMethodInput.value);
    });

    // Fetch Sales Data
    async function fetchSales(page, searchTerm = '', date = '', paymentMethod = '') {
        currentPage = page;
        const tbody = document.getElementById('sales-data');
        tbody.innerHTML = '<tr><td colspan="8"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div></td></tr>';

        try {
            let url = `../api/get_sales_data.php?page=${page}&limit=${salesPerPage}`;
            if (searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }
            if (date) {
                url += `&date=${date}`;
            }
            if (paymentMethod) {
                url += `&payment_method=${encodeURIComponent(paymentMethod)}`;
            }
            const response = await fetch(url);
            const data = await response.json();

            tbody.innerHTML = '';

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }

            if (data.sales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron ventas.</td></tr>';
            } else {
                data.sales.forEach(sale => {
                    const row = tbody.insertRow();
                    const saleDate = new Date(sale.Fecha_venta);
                    row.innerHTML = `
                        <td>${sale.ID_venta}</td>
                        <td>${saleDate.toLocaleDateString()} ${saleDate.toLocaleTimeString()}</td>
                        <td>${escapeHTML(sale.Nombre_cliente)}</td>
                        <td>${escapeHTML(sale.Cedula_Rif)}</td>
                        <td>${escapeHTML(sale.Metodo_pago)}</td>
                        <td>${escapeHTML(sale.Referencia_pago) || 'N/A'}</td>
                        <td>Bs ${parseFloat(sale.Total_venta).toFixed(2)}</td>
                        <td></td>
                    `;

                    // Contenedor para botones de acciones
                    const btnGroup = document.createElement('div');
                    btnGroup.className = 'btn-group';

                    // Botón de Ver Detalles
                    const detailsButton = document.createElement('button');
                    detailsButton.innerHTML = '<i class="bi bi-eye-fill"></i>';
                    detailsButton.className = 'btn btn-sm btn-outline-info';
                    detailsButton.title = 'Ver Detalles';
                    detailsButton.onclick = () => showSaleDetails(sale.ID_venta);

                    // Botón de PDF
                    const pdfButton = document.createElement('a');
                    pdfButton.innerHTML = '<i class="bi bi-file-earmark-pdf-fill"></i>';
                    pdfButton.className = 'btn btn-sm btn-outline-danger';
                    pdfButton.title = 'Descargar Recibo';
                    pdfButton.target = '_blank';
                    pdfButton.href = `../src/core/generate_receipt.php?id=${sale.ID_venta}`;

                    btnGroup.appendChild(detailsButton);
                    btnGroup.appendChild(pdfButton);
                    row.cells[7].appendChild(btnGroup);
                });
            }

            renderPagination(data.totalPages, page, searchTerm);

        } catch (error) {
            console.error("Fetch sales error:", error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar los datos.</td></tr>';
        }
    }

    // Show Sale Details Modal
    async function showSaleDetails(saleId) {
        try {
            const response = await fetch(`../api/get_sales_details.php?id=${saleId}`);
            const data = await response.json();

            if (data.error) {
                // Asumiendo que showToast existe en utils.js o similar
                if (typeof showToast === 'function') {
                    showToast('Error', data.error, 'danger');
                } else {
                    alert(data.error);
                }
                return;
            }

            // Populate Modal
            document.getElementById('modal-sale-id').textContent = data.id_venta;
            document.getElementById('modal-sale-date').textContent = new Date(data.fecha_venta).toLocaleString();
            document.getElementById('modal-client-name').textContent = escapeHTML(data.Nombre_cliente);
            document.getElementById('modal-client-id').textContent = escapeHTML(data.Cedula_Rif);
            document.getElementById('modal-payment-method').textContent = escapeHTML(data.metodo_pago);
            document.getElementById('modal-payment-reference').textContent = escapeHTML(data.referencia_pago) || 'N/A';
            document.getElementById('modal-sale-total').textContent = `Bs ${parseFloat(data.total_venta).toFixed(2)}`;

            const productsList = document.getElementById('modal-products-list');
            productsList.innerHTML = '';
            data.details.forEach(item => {
                productsList.innerHTML += `
                    <tr>
                        <td>${escapeHTML(item.Nombre_Producto)}</td>
                        <td>${item.Cantidad_producto}</td>
                        <td>$${parseFloat(item.Precio_unitario_venta).toFixed(2)}</td>
                        <td>$${parseFloat(item.Subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            detailsModal.show();

        } catch (error) {
            console.error('Error fetching sale details:', error);
        }
    }

    // Render Pagination
    function renderPagination(totalPages, currentPage, searchTerm) {
        const paginationControls = document.getElementById('pagination-controls');
        paginationControls.innerHTML = '';

        const createPageItem = (text, page, isDisabled = false, isActive = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${isDisabled ? 'disabled' : ''} ${isActive ? 'active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = text;
            if (!isDisabled) {
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchSales(page, searchTerm);
                });
            }
            li.appendChild(a);
            return li;
        };

        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center';

        ul.appendChild(createPageItem('&laquo;', currentPage - 1, currentPage === 1));

        for (let i = 1; i <= totalPages; i++) {
            ul.appendChild(createPageItem(i, i, false, i === currentPage));
        }

        ul.appendChild(createPageItem('&raquo;', currentPage + 1, currentPage === totalPages));

        paginationControls.appendChild(ul);
    }
});