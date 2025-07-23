<?php

namespace App\Controllers;


use App\Core\AuthService;
use App\Models\Colaborador;
use App\Models\PasswordReset;

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
            $colaborador = (new Colaborador())->findByEmail($email);

            if (!$colaborador) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se encontró un colaborador con ese correo.', 'icon' => 'error'];
            } else {
                $token = (new AuthService())->generateResetToken($email);

                // Se añade la acción correcta a la URL del enlace.
                $resetLink = BASE_URL . 'index.php?route=reset-password&action=showResetPasswordForm&token=' . $token;

                $_SESSION['mensaje_sa2'] = [
                    'title' => '¡Simulación Exitosa!',
                    'html' => 'Copia y pega este enlace en tu navegador:<br><br><input type="text" class="form-control" value="' . $resetLink . '" readonly>',
                    'icon' => 'info'
                ];
            }
            header('Location: ' . BASE_URL . 'index.php?route=forgot-password&action=showForgotPasswordForm');
            exit;
        }
    }

    /**
     * Muestra el formulario final para restablecer la contraseña si el token es válido.
     */
    public function showResetPasswordForm()
    {
        $token = $_GET['token'] ?? '';
        $resetRecord = (new PasswordReset())->findByToken($token);

        if (!$resetRecord || new \DateTime() > new \DateTime($resetRecord['expires_at'])) {
            http_response_code(403);
            require_once '../src/Views/error-403.php';
            exit;
        }

        $this->render('Views/auth/reset_password.php', [
            'pageTitle' => 'Restablecer Contraseña',
            'token' => $token,
            'formId' => 'form-reset-password'
        ], false);
    }

    /**
     * Muestra el mensaje de éxito después de restablecer la contraseña.
     */
    public function showResetSuccess()
    {
        $this->render('Views/auth/reset_success.php', ['pageTitle' => 'Éxito'], false);
    }

    /**
     * Procesa el formulario final y actualiza la contraseña.
     */
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($newPassword !== $confirmPassword || empty($newPassword)) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Las contraseñas no coinciden o están vacías.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=reset-password&token=' . $token);
                exit;
            }

            $resetRecord = (new PasswordReset())->findByToken($token);

            if (!$resetRecord || new \DateTime() > new \DateTime($resetRecord['expires_at'])) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'El enlace ha expirado o no es válido.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=login');
                exit;
            }

            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            (new Colaborador())->updatePasswordByEmail($resetRecord['email'], $newPasswordHash);
            (new PasswordReset())->deleteToken($token);

            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Tu contraseña ha sido restablecida.', 'icon' => 'success'];
             header('Location: ' . BASE_URL . 'index.php?route=reset-password&action=showResetSuccess');
            exit;
        }
    }
}
