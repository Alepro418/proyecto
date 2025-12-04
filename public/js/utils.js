document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const togglePassword = document.getElementById('toggle');

    if (passwordField && togglePassword) {
        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye-fill');
            this.classList.toggle('bi-eye-slash-fill');
        });
    }
});

function showToast(title, message, type = 'info') {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toastContainer.style.zIndex = 1100;

    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-${type}">${title}</strong>
                <small>Ahora</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Use a temporary div to safely parse the HTML string
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = toastHTML;
    
    const toastEl = tempDiv.firstChild;

    // Append the actual toast element to a container that will be added to the body
    toastContainer.appendChild(toastEl);
    document.body.appendChild(toastContainer);

    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();

    // Clean up the container after the toast is hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastContainer.remove();
    });
}

function escapeHTML(str) {
    if (str === null || str === undefined) {
        return '';
    }
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
