document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const suppliersPerPage = 15;

    const searchInput = document.getElementById('search-input');

    fetchSuppliers(currentPage, '');

    searchInput.addEventListener('input', () => {
        fetchSuppliers(1, searchInput.value.trim());
    });

    async function fetchSuppliers(page, searchTerm = '') {
        currentPage = page;
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '<tr><td colspan="6"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div></td></tr>';

        try {
            let url = `../api/get_suppliers_data.php?page=${page}&limit=${suppliersPerPage}`;
            if (searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }
            const response = await fetch(url);
            const data = await response.json();

            tbody.innerHTML = '';

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }

            if (data.suppliers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No se encontraron proveedores.</td></tr>';
            } else {
                data.suppliers.forEach(supplier => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>${escapeHTML(supplier.nombre_proveedor)}</td>
                        <td>${escapeHTML(supplier.rif)}</td>
                        <td>${escapeHTML(supplier.telefono)}</td>
                        <td>${escapeHTML(supplier.correo)}</td>
                        <td>${escapeHTML(supplier.ciudad)}</td>
                        <td>
                            <button class="btn btn-primary btn-sm view-products-btn" data-supplier-id="${supplier.ID_proveedor}"><i class="bi bi-box-seam"></i> Ver Productos</button>   
                        </td>
                    `;
                });
            }

            renderPagination(data.totalSuppliers, data.limit, page, searchTerm);

        } catch (error) {
            console.error("Fetch suppliers error:", error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar los datos.</td></tr>';
        }
    }

    document.querySelector('table tbody').addEventListener('click', async function(event) {
        if (event.target.classList.contains('view-products-btn')) {
            const supplierId = event.target.dataset.supplierId;
            const productsTbody = document.getElementById('products-tbody');
            productsTbody.innerHTML = '<tr><td colspan="3"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div></td></tr>';

            try {
                const response = await fetch(`../api/get_supplier_products.php?supplier_id=${supplierId}`);
                const products = await response.json();

                productsTbody.innerHTML = '';

                if (products.error) {
                    productsTbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${products.error}</td></tr>`;
                    return;
                }

                if (products.length === 0) {
                    productsTbody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron productos para este proveedor.</td></tr>';
                } else {
                    products.forEach(product => {
                        const row = productsTbody.insertRow();
                        row.innerHTML = `
                            <td>${escapeHTML(product.nombre_producto)}</td>
                            <td>${escapeHTML(product.descripcion)}</td>
                            <td class="text-center">${escapeHTML(product.cantidad)}</td>
                        `;
                    });
                }

                const productsModal = new bootstrap.Modal(document.getElementById('productsModal'));
                productsModal.show();

            } catch (error) {
                console.error("Fetch supplier products error:", error);
                productsTbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al cargar los productos.</td></tr>';
            }
        }
    });

    function renderPagination(totalItems, itemsPerPage, currentPage, searchTerm) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
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
                    fetchSuppliers(page, searchTerm);
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
