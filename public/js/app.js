// Espera a que todo el contenido de la página (DOM) esté cargado antes de ejecutar cualquier script.
document.addEventListener('DOMContentLoaded', function() {

    // --- Lógica para el modo de edición de UBICACIÓN en el perfil ---
    const viewLocationDiv = document.getElementById('view-location-div');
    const editLocationDiv = document.getElementById('edit-location-div');
    const btnChangeLocation = document.getElementById('btn-change-location');
    const btnCancelLocation = document.getElementById('btn-cancel-location');

    // Se ejecuta solo si encuentra los elementos en la página actual.
    if (viewLocationDiv && editLocationDiv && btnChangeLocation && btnCancelLocation) {
        btnChangeLocation.addEventListener('click', function() {
            viewLocationDiv.style.display = 'none';
            editLocationDiv.style.display = 'block';
        });
        btnCancelLocation.addEventListener('click', function() {
            editLocationDiv.style.display = 'none';
            viewLocationDiv.style.display = 'flex';
        });
    }

    // --- Lógica para el modo de edición de CONTRASEÑA en el perfil ---
    const viewPasswordDiv = document.getElementById('view-password-div');
    const editPasswordDiv = document.getElementById('edit-password-div');
    const btnChangePassword = document.getElementById('btn-change-password');
    const btnCancelPassword = document.getElementById('btn-cancel-password');

    // Se ejecuta solo si encuentra los elementos en la página actual.
    if (viewPasswordDiv && editPasswordDiv && btnChangePassword && btnCancelPassword) {
        btnChangePassword.addEventListener('click', function() {
            viewPasswordDiv.style.display = 'none';
            editPasswordDiv.style.display = 'block';
        });
        btnCancelPassword.addEventListener('click', function() {
            editPasswordDiv.style.display = 'none';
            viewPasswordDiv.style.display = 'flex';
        });
    }

    // --- Lógica REUTILIZABLE para los botones de "ojo" de las contraseñas ---
    function setupPasswordToggle(inputId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(buttonId);

        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye-fill');
                this.querySelector('i').classList.toggle('bi-eye-slash-fill');
            });
        }
    }

    // Se inicializa la funcionalidad para cada campo de contraseña que exista en la página.
    setupPasswordToggle('current_password', 'toggleCurrentPassword');
    setupPasswordToggle('new_password', 'toggleNewPassword');
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');

    // --- Lógica para el modal de NOTAS en la tabla de inventario ---
    const notesModal = document.getElementById('notesModal');
    if (notesModal) {
        notesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const notes = button.getAttribute('data-notes');
            const modalBody = notesModal.querySelector('#notesModalBody');
            modalBody.textContent = notes;
        });
    }

    // --- Lógica para generar una IP aleatoria ---
    const btnGenerateIp = document.getElementById('btn-generate-ip');
    const ipInput = document.getElementById('ip_asignada');

    if (btnGenerateIp && ipInput) {
        btnGenerateIp.addEventListener('click', function() {
            // Genera 4 números aleatorios entre 1 y 254
            const octet1 = Math.floor(Math.random() * 254) + 1;
            const octet2 = Math.floor(Math.random() * 255);
            const octet3 = Math.floor(Math.random() * 255);
            const octet4 = Math.floor(Math.random() * 254) + 1;

            // Une los números con puntos y los pone en el campo de texto
            ipInput.value = `${octet1}.${octet2}.${octet3}.${octet4}`;
        });
    }
});