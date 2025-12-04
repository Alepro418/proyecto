document.addEventListener('DOMContentLoaded', function() {
    fetchLowStockProducts();
});

async function fetchLowStockProducts() {
    const notificationsContainer = document.getElementById('low-stock-notifications');
    if (!notificationsContainer) return; // Salir si el contenedor no existe en la página actual

    notificationsContainer.innerHTML = ''; // Limpiar notificaciones anteriores

    try {
        // Endpoint que devuelve solo los productos con stock bajo
        const response = await fetch('../api/get_low_stock_products.php');

        if (!response.ok) {
            throw new Error(`Error HTTP! Estado: ${response.status}`);
        }

        const text = await response.text();
        // Intentar parsear el JSON, si falla, mostrará un error más claro.
        const data = text ? JSON.parse(text) : [];

        if (data.error) {
            notificationsContainer.innerHTML = `<p class="notification alert alert-danger">Error: ${data.error}</p>`;
            return;
        }

        if (data.length === 0) {
            notificationsContainer.innerHTML = '<div class="alert alert-success" role="alert">No hay alarmas de stock en este momento.</div>';
            return;
        }

        data.forEach(product => {
            const notification = document.createElement('div');
            notification.className = 'notification alert alert-warning'; // Usando clases de Bootstrap
            notification.innerHTML = `<strong>¡ALERTA DE STOCK BAJO!</strong> El producto "<strong>${product.Producto}</strong>" tiene solo <strong>${product.Cantidad}</strong> unidades restantes.`;
            notificationsContainer.appendChild(notification);
        });

    } catch (error) {
        console.error("Error al obtener las alarmas:", error);
        notificationsContainer.innerHTML = `<p class="notification alert alert-danger">Error al cargar las alarmas. Por favor, inténtalo de nuevo más tarde.</p>`;
    }
}