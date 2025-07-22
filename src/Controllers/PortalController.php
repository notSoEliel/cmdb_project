<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Asignacion; // Asegúrate de que esta línea exista para usar el modelo de Asignacion
use App\Models\InventarioImagen; // Asegúrate de que esta línea exista para usar el modelo de InventarioImagen

class PortalController extends BaseController
{

    /**
     * Muestra el dashboard principal del colaborador.
     */
    public function index()
    {
        $this->render('Views/portal/index.php', [
            'pageTitle' => 'Portal del Colaborador'
        ]);
    }

    /**
     * Muestra la página de perfil del colaborador logueado.
     */
    public function showProfile()
    {
        $colaboradorId = $_SESSION['user_id'] ?? 0;
        $colaborador = (new \App\Models\Colaborador())->findById($colaboradorId);

        $this->render('Views/portal/perfil.php', [
            'pageTitle' => 'Mi Perfil',
            'colaborador' => $colaborador,
            'formIds' => ['form-location', 'form-password']
        ]);
    }

    /**
     * Muestra la lista de equipos asignados al colaborador logueado.
     */
    public function misEquipos()
    {
        $inventarioModel = new Inventario();
        $colaboradorId = $_SESSION['user_id'] ?? 0;

        // Opciones para filtrar el inventario por el ID del colaborador
        $options = [
            'paginate' => false, // No necesitamos paginación aquí
            'filters' => ['a.colaborador_id' => $colaboradorId]
        ];

        // Obtenemos los equipos usando el método que ya existe
        $equipos = $inventarioModel->findAll($options);

        // Renderizamos la nueva vista y le pasamos los datos
        $this->render('Views/portal/mis_equipos.php', [
            'pageTitle' => 'Mis Equipos Asignados',
            'equipos' => $equipos
        ]);
    }

    /**
     * Muestra todas las imágenes de un equipo específico asignado al colaborador logueado.
     * Incluye una validación de seguridad para asegurar que el equipo pertenece al colaborador.
     */
    public function showEquipoImages()
    {
        $inventario_id = (int)($_GET['id'] ?? 0);
        $colaboradorId = $_SESSION['user_id'] ?? 0; // ID del colaborador logueado

        $inventarioModel = new Inventario();
        $inventarioImagenModel = new InventarioImagen();
        $asignacionModel = new Asignacion();

        // 1. Verificar si el equipo existe
        $equipo = $inventarioModel->findById($inventario_id);
        if (!$equipo) {
            http_response_code(404);
            require_once '../src/Views/error-404.php';
            exit;
        }

        // 2. Validar que el equipo esté asignado al colaborador actual
        $isAssigned = $asignacionModel->isEquipoAssignedToColaborador($inventario_id, $colaboradorId);

        if (!$isAssigned) {
            // Si el equipo no está asignado al colaborador logueado, se deniega el acceso.
            http_response_code(403); // Acceso denegado
            require_once '../src/Views/error-403.php';
            exit;
        }

        // 3. Obtener las imágenes del equipo
        $imagenes = $inventarioImagenModel->findByInventarioId($inventario_id);

        // 4. Renderizar la vista de imágenes (reutilizando la existente)
        $this->render('Views/inventario/imagenes.php', [
            'pageTitle' => 'Imágenes de ' . htmlspecialchars($equipo['nombre_equipo']),
            'equipo' => $equipo,
            'imagenes' => $imagenes,
            'isPortalView' => true // Una bandera para la vista, si necesita ajustar algo para el portal
        ]);
    }


    /**
     * Procesa la actualización de la ubicación del colaborador.
     */
    public function updateLocation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaboradorId = $_SESSION['user_id'] ?? 0;
            $nuevaUbicacion = $_POST['ubicacion'] ?? '';

            if ($colaboradorId) {
                $model = new \App\Models\Colaborador();
                // 1. Obtener el colaborador para ver su ubicación actual
                $colaborador = $model->findById($colaboradorId);

                // 2. Solo actualizar si la nueva ubicación es diferente a la actual
                if ($colaborador && $colaborador['ubicacion'] !== $nuevaUbicacion) {
                    $model->updateLocation($colaboradorId, $nuevaUbicacion);
                    $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Ubicación actualizada.', 'icon' => 'success'];
                }
            }
        }
        // Redirige de vuelta al perfil
        header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
        exit;
    }

    /**
     * Procesa el formulario de cambio de contraseña para el colaborador logueado.
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Si no es una solicitud POST, no hacer nada.
            header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
            exit;
        }

        // 1. Recoger datos y ID de sesión
        $colaboradorId = $_SESSION['user_id'] ?? 0;
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // 2. Validaciones básicas
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Todos los campos son obligatorios.', 'icon' => 'error'];
            header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'Las nuevas contraseñas no coinciden.', 'icon' => 'error'];
            header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
            exit;
        }

        // 3. Verificación de la contraseña actual
        $model = new \App\Models\Colaborador();
        $colaborador = $model->findById($colaboradorId);

        if (!$colaborador || !password_verify($currentPassword, $colaborador['password_hash'])) {
            $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'La contraseña actual es incorrecta.', 'icon' => 'error'];
            header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
            exit;
        }

        // 4. Si todo es correcto, hashear y guardar la nueva contraseña
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $model->updatePassword($colaboradorId, $newPasswordHash);

        $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Tu contraseña ha sido actualizada.', 'icon' => 'success'];
        header('Location: ' . BASE_URL . 'index.php?route=portal&action=showProfile');
        exit;
    }
}
