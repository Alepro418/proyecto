document.addEventListener('DOMContentLoaded', function() {
    let productCount = 1;
    const addProductBtn = document.getElementById('add-product');
    const productDetailsContainer = document.getElementById('product-details-container');
    const grandTotalSpan = document.getElementById('grand-total');
    const hiddenGrandTotalInput = document.getElementById('hidden_grand_total');
    const payMethodSelect = document.getElementById('pay_method');
    const referenciaDiv = document.getElementById('referencia-pago-movil');
    const referenciaInput = document.getElementById('referencia');

    // Lógica para mostrar/ocultar el campo de referencia
    payMethodSelect.addEventListener('change', function() {
        if (this.value === 'Pago Movil' || 
            this.value === 'Transferencia' || 
            this.value === 'Tarjeta de Debito' || 
            this.value === 'Tarjeta de Credito'
        ) {
            referenciaDiv.style.display = 'block';
            referenciaInput.setAttribute('required', 'required');
        } else {
            referenciaDiv.style.display = 'none';
            referenciaInput.removeAttribute('required');
            referenciaInput.value = ''; // Limpiar el valor cuando se oculta
        }
    });

    // Fetch all products on load to populate the datalist
    fetch('../api/get_products_data.php')
    .then(response => response.json())
    .then(data => {
        console.log('Datos de productos recibidos:', data); // Para depuración
        
        const datalist = document.getElementById('products-list');
        // Limpia el datalist primero
        datalist.innerHTML = '';
        
        data.products.forEach(product => {
            const option = document.createElement('option');
            // Usa los nombres EXACTOS de los campos que ves en la API
            option.value = product.Nombre_Producto; // Nombre exacto del campo
            option.setAttribute('data-id', product.ID_Producto); // ID exacto del campo
            option.setAttribute('data-price', product.Precio_de_Salida); // Precio exacto del campo
            option.setAttribute('data-stock', product.Cantidad); // Stock exacto del campo
            
            // Agrega datos adicionales para depuración si es necesario
            option.setAttribute('data-codigo', product.Codigo);
            
            datalist.appendChild(option);
        });
        
        console.log('Número de productos cargados:', data.products.length); // Verifica cuántos productos se cargaron
    })
    .catch(error => {
        console.error('Error fetching product list for datalist:', error);
        alert('Error al cargar la lista de productos para selección. Inténtalo de nuevo.');
    });


    // Function to update subtotal for a single product item
    function updateProductSubtotal(productItem) {
        const quantityInput = productItem.querySelector('input[name="product_quantity[]"]');
        const priceInput = productItem.querySelector('input[name="product_price[]"]');
        const subtotalInput = productItem.querySelector('input[name="product_subtotal[]"]');
        const stockInfoSpan = productItem.querySelector('.stock-info');

        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const stock = parseFloat(priceInput.getAttribute('data-stock')) || 0; // Get original stock from price input's data-stock

        // Basic stock validation (more robust validation should be on the server)
        if (quantity > stock && stockInfoSpan) {
            stockInfoSpan.style.color = 'red';
            stockInfoSpan.textContent = `Stock insuficiente. Disponible: ${stock}`;
        } else if (stockInfoSpan) {
            stockInfoSpan.style.color = 'gray';
            stockInfoSpan.textContent = `Stock disponible: ${stock}`;
        }


        const subtotal = quantity * price;
        subtotalInput.value = subtotal.toFixed(2);
    }

    // Function to update the grand total of the entire sale
    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.product-item input[name="product_subtotal[]"]').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        grandTotalSpan.textContent = grandTotal.toFixed(2);
        hiddenGrandTotalInput.value = grandTotal.toFixed(2); // Update hidden input for form submission

        const hiddenSubtotalInput = document.getElementById('hidden_subtotal');
        if (hiddenSubtotalInput) {
            hiddenSubtotalInput.value = grandTotal.toFixed(2);
        }
    }

    // Event listener for adding new product items
        addProductBtn.addEventListener('click', function() {
        productCount++;
        const newProductDiv = document.createElement('div');
        newProductDiv.classList.add('product-item', 'border', 'rounded', 'p-3', 'mb-3');
        newProductDiv.setAttribute('data-product-id', productCount);
        newProductDiv.innerHTML = `
            <button type="button" class="btn btn-danger btn-sm float-end remove-product-btn">X</button>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="product_name_${productCount}" class="form-label">Nombre del producto:</label>
                    <input type="text" id="product_name_${productCount}" name="product_name[]" list="products-list" class="form-control product-name-input" required>
                    <input type="hidden" name="product_id[]" class="product-id-input">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="product_price_${productCount}" class="form-label">Precio Unitario:</label>
                    <input type="number" id="product_price_${productCount}" name="product_price[]" step="0.01" min="0" readonly required class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="product_quantity_${productCount}" class="form-label">Cantidad:</label>
                    <input type="number" id="product_quantity_${productCount}" name="product_quantity[]" min="1" step="1" required class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="product_subtotal_${productCount}" class="form-label">Subtotal:</label>
                    <input type="number" id="product_subtotal_${productCount}" name="product_subtotal[]" step="0.01" min="0" readonly class="form-control">
                </div>
            </div>
            <span class="stock-info text-muted">Stock disponible: --</span>
        `;
        productDetailsContainer.appendChild(newProductDiv);
    });

    // Event listener for changes in product name and quantity (using event delegation)
    productDetailsContainer.addEventListener('input', function(event) {
        // Handle product name input changes (for auto-filling price)
        if (event.target.classList.contains('product-name-input')) {
            const selectedOption = document.querySelector(`#products-list option[value="${event.target.value}"]`);
            const productItem = event.target.closest('.product-item');
            const productIdInput = productItem.querySelector('.product-id-input');
            const priceInput = productItem.querySelector('input[name="product_price[]"]');
            const quantityInput = productItem.querySelector('input[name="product_quantity[]"]');
            const stockInfoSpan = productItem.querySelector('.stock-info');

            if (selectedOption) {
                const productId = selectedOption.getAttribute('data-id');
                const productPrice = selectedOption.getAttribute('data-price');
                const productStock = selectedOption.getAttribute('data-stock');

                productIdInput.value = productId;
                priceInput.value = parseFloat(productPrice).toFixed(2);
                priceInput.setAttribute('data-stock', productStock); // Store stock in price input
                stockInfoSpan.textContent = `Stock disponible: ${productStock}`;
                stockInfoSpan.style.color = 'gray'; // Reset color

                // Reset quantity if it was previously invalid or too high
                if (parseFloat(quantityInput.value) > parseFloat(productStock) || parseFloat(quantityInput.value) <= 0) {
                    quantityInput.value = 1; // Default to 1
                }

                updateProductSubtotal(productItem);
                updateGrandTotal();
            } else {
                // If no valid product is selected, clear price, ID, and subtotal
                productIdInput.value = '';
                priceInput.value = '';
                priceInput.removeAttribute('data-stock'); // Clear stock info
                stockInfoSpan.textContent = `Stock disponible: --`;
                stockInfoSpan.style.color = 'gray';
                updateProductSubtotal(productItem);
                updateGrandTotal();
            }
        }

        // Check if the input is a quantity field within a product-item
        if (event.target.name === 'product_quantity[]') {
            const productItem = event.target.closest('.product-item');
            if (productItem) {
                updateProductSubtotal(productItem);
                updateGrandTotal();
            }
        }
    });

    // Event listener for removing product items (using event delegation)
    productDetailsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-product-btn')) {
            const productItemToRemove = event.target.closest('.product-item');
            // Prevent removing the first (fixed) product item by checking data-product-id
            if (productItemToRemove && productItemToRemove.getAttribute('data-product-id') !== '1') {
                productItemToRemove.remove();
                updateGrandTotal(); // Recalculate total after removing
            } else if (productItemToRemove) {
                alert("No puedes eliminar el primer producto. Edita sus valores si no es necesario.");
            }
        }
    });

    // Initial calculation on page load for the first product item
    updateProductSubtotal(document.querySelector('.product-item[data-product-id="1"]'));
    updateGrandTotal();
});