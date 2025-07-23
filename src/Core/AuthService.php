<?php

namespace App\Core;

use App\Models\Usuario;
use App\Models\Colaborador;
use App\Models\HistorialLogin;
use App\Models\PasswordReset;

class AuthService
{
    /**
     * Intenta iniciar sesión buscando en la tabla de administradores y luego en la de colaboradores.
     *
     * @param string $email El email proporcionado por el usuario.
     * @param string $password La contraseña proporcionada.
     * @return bool True si el login es exitoso, false si no.
     */
    public function attemptLogin(string $email, string $password): bool
    {
        // Primero, intenta encontrar un administrador
        $usuarioModel = new Usuario();
        $user = $usuarioModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Si es un admin y la contraseña es correcta, inicia la sesión de admin
            $this->startSession($user, 'admin');
            return true;
        }

        // Si no es un admin, intenta encontrar un colaborador
        $colaboradorModel = new Colaborador();
        $user = $colaboradorModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Si es un colaborador y la contraseña es correcta, inicia la sesión de colaborador
            $this->startSession($user, 'colaborador');
            // Se registra el inicio de sesión en el historial
            (new HistorialLogin())->save($user['id']);
            return true;
        }

        // Si no se encontró en ninguna tabla o la contraseña es incorrecta
        return false;
    }

    /**
     * Cierra la sesión del usuario actual.
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Verifica si hay un usuario logueado.
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Obtiene el rol del usuario actual.
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Almacena la información del usuario en la sesión.
     *
     * @param array $user Los datos del usuario desde la base de datos.
     * @param string $role El rol a asignar ('admin' o 'colaborador').
     */
    private function startSession(array $user, string $role): void
    {
        session_regenerate_id(true); // Previene ataques de fijación de sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_role'] = $role;
    }

    /**
     * Genera un token de reseteo seguro, lo guarda en la BD y lo devuelve.
     * @param string $email
     * @return string El token generado.
     */
    public function generateResetToken(string $email): string
    {
        $passwordResetModel = new PasswordReset();

        // 1. Genera un token aleatorio y seguro
        $token = bin2hex(random_bytes(32));

        // 2. Define una fecha de expiración (ej: 1 hora a partir de ahora)
        $expiresAt = (new \DateTime())->modify('+1 hour')->format('Y-m-d H:i:s');

        // 3. Guarda el token en la base de datos
        $passwordResetModel->saveToken($email, $token, $expiresAt);

        return $token;
    }
}
