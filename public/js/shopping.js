document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const itemsPerPage = 15;

    const searchInput = document.getElementById('search-input');
    const dateInput = document.getElementById('date-input');

    fetchData(currentPage, '', '');

    searchInput.addEventListener('input', () => {
        dateInput.value = ''; 
        fetchData(1, searchInput.value.trim(), '');
    });

    dateInput.addEventListener('change', () => {
        searchInput.value = ''; 
        fetchData(1, '', dateInput.value);
    });

    async function fetchData(page, searchTerm = '', date = '') {
        currentPage = page;
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = '<tr><td colspan="6"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div></td></tr>';

        try {
            let url = `../api/get_shopping_data.php?page=${page}&limit=${itemsPerPage}`;
            if (searchTerm) {
                url += `&search=${encodeURIComponent(searchTerm)}`;
            }
            if (date) {
                url += `&date=${date}`;
            }
            const response = await fetch(url);
            const data = await response.json();

            tbody.innerHTML = '';

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }

            if (data.purchases.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron compras.</td></tr>';
            } else {
                data.purchases.forEach(purchase => {
                    console.log(purchase);
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>${purchase.Fecha_compra}</td>
                        <td>${escapeHTML(purchase.Nombre_Producto)}</td>
                        <td>${escapeHTML(purchase.Nombre_proveedor)}</td>
                        <td>${purchase.Cantidad_comprada}</td>
                        <td>Bs ${parseFloat(purchase.Precio_unitario_compra).toFixed(2)}</td>
                        <td>Bs ${parseFloat(purchase.Total_compra).toFixed(2)}</td>
                        <td>${escapeHTML(purchase.Tipo_compra)}</td>
                    `;
                });
            }

            renderPagination(data.totalPages, page, searchTerm, date);

        } catch (error) {
            console.error("Fetch error:", error);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos.</td></tr>';
        }
    }

    function renderPagination(totalPages, currentPage, searchTerm, date) {
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
                    fetchData(page, searchTerm, date);
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
