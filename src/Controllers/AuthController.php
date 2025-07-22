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
}