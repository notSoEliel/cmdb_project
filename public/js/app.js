// Espera a que el DOM esté cargado para ejecutar el script
document.addEventListener('DOMContentLoaded', function() {

    // Busca el botón del ojo y el campo de contraseña por sus IDs
    const togglePasswordButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('togglePasswordIcon');

    // Si no encuentra el botón en la página, no hace nada más
    if (!togglePasswordButton) {
        return;
    }

    // Añade un evento 'click' al botón
    togglePasswordButton.addEventListener('click', function() {
        // Revisa el tipo actual del campo de contraseña
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Cambia el ícono del ojo
        if (type === 'password') {
            passwordIcon.classList.remove('bi-eye-slash-fill');
            passwordIcon.classList.add('bi-eye-fill');
        } else {
            passwordIcon.classList.remove('bi-eye-fill');
            passwordIcon.classList.add('bi-eye-slash-fill');
        }
    });
});