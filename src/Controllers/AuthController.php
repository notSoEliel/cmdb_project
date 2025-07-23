<?php

namespace App\Controllers;

use App\Core\AuthService;

class AuthController extends BaseController
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function index()
    {
        // El 'false' al final le dice al método que no use el layout del dashboard.
        $this->render('Views/auth/login.php', ['pageTitle' => 'Iniciar Sesión'], false);
    }

    /**
     * Procesa el intento de inicio de sesión.
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $authService = new AuthService();
            if ($authService->attemptLogin($email, $password)) {
                // Si el login es exitoso, redirige según el rol
                if ($authService->getRole() === 'admin') {
                    header('Location: ' . BASE_URL); // Al dashboard de admin
                } else {
                    header('Location: ' . BASE_URL . 'index.php?route=portal'); // Al futuro portal de colaborador
                }
                exit;
            } else {
                // Si el login falla, muestra un error
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Credenciales incorrectas.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=login');
                exit;
            }
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout()
    {
        (new AuthService())->logout();
        header('Location: ' . BASE_URL . 'index.php?route=login');
        exit;
    }

    /**
     * Muestra el formulario para solicitar el reseteo de contraseña.
     */
    public function showForgotPasswordForm()
    {
        $this->render('Views/auth/forgot_password.php', [
            'pageTitle' => 'Recuperar Contraseña',
            'formId' => 'form-forgot-password'
        ], false);
    }

    /**
     * Procesa la solicitud de reseteo de contraseña.
     */
    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $isSimulation = isset($_POST['simulate_email']);

            $colaborador = (new \App\Models\Colaborador())->findByEmail($email);

            if (!$colaborador) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se encontró ningún colaborador con ese correo.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=forgot-password');
                exit;
            }

            // Lógica para generar y guardar el token (la crearemos en el siguiente paso)
            // $token = $this->authService->generateResetToken($email);
            $token = "SIMULATED_TOKEN_12345"; // Marcador de posición por ahora

            if ($isSimulation) {
                $resetLink = BASE_URL . 'index.php?route=reset-password&token=' . $token;
                $_SESSION['mensaje_sa2'] = [
                    'title' => '¡Simulación Exitosa!',
                    'html' => 'En un sistema real, se enviaría este enlace a tu correo:<br><br><a href="' . $resetLink . '">' . $resetLink . '</a>',
                    'icon' => 'info'
                ];
            } else {
                // Aquí iría la lógica para enviar el correo real con PHPMailer
                $_SESSION['mensaje_sa2'] = ['title' => '¡Solicitud Recibida!', 'text' => 'Si el correo existe, recibirás un enlace en breve.', 'icon' => 'success'];
            }

            header('Location: ' . BASE_URL . 'index.php?route=forgot-password');
            exit;
        }
    }
}
