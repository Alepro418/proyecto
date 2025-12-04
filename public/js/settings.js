document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('settings-form');
    const messageDiv = document.createElement('div');
    form.prepend(messageDiv);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        messageDiv.innerHTML = '';

        const formData = new FormData(form);

        fetch('../src/core/process_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            let successMessage = 'Configuración guardada con éxito.';
            if (data.success_username) successMessage += '<br>' + data.success_username;
            if (data.success_password) successMessage += '<br>' + data.success_password;
            if (data.success_theme) successMessage += '<br>' + data.success_theme;

            messageDiv.innerHTML = `<div class="alert alert-success">${successMessage}</div>`;

            // Optioonally, clear password field after successful update
            document.getElementById('password').value = '';

        })
        .catch(error => {
            messageDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
        });
    });
});