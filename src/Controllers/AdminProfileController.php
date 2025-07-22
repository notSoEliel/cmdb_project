<?php
namespace App\Controllers;
use App\Models\Usuario;

class AdminProfileController extends BaseController
{
    /**
     * Muestra la página de perfil del administrador logueado.
     */
    public function index()
    {
        $adminId = $_SESSION['user_id'] ?? 0;
        $admin = (new Usuario())->findById($adminId);

        $this->render('Views/admin/profile/index.php', [
            'pageTitle' => 'Mi Perfil de Administrador',
            'admin' => $admin,
            'formIds' => ['form-admin-password'],
        ]);
    }

    /**
     * Procesa el cambio de contraseña para el administrador.
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['user_id'] ?? 0;
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword) || $newPassword !== $confirmPassword) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Por favor, rellena todos los campos correctamente.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=admin_profile');
                exit;
            }

            $model = new Usuario();
            $admin = $model->findById($adminId);

            if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {
                $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'La contraseña actual es incorrecta.', 'icon' => 'error'];
                header('Location: ' . BASE_URL . 'index.php?route=admin_profile');
                exit;
            }

            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $model->updatePassword($adminId, $newPasswordHash);

            $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Contraseña actualizada.', 'icon' => 'success'];
            header('Location: ' . BASE_URL . 'index.php?route=admin_profile');
            exit;
        }
    }
}