<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Asignacion;
use App\Models\InventarioImagen;
use App\Models\Colaborador;
use App\Models\Necesidad;

class PortalController extends BaseController
{

    /**
     * Muestra el dashboard principal del colaborador.
     */
    public function index()
    {
        $colaboradorId = $_SESSION['user_id'] ?? 0; // Obtiene el ID del colaborador logueado
        $colaboradorModel = new Colaborador();
        $inventarioModel = new Inventario();
        $necesidadModel = new Necesidad();

        // Obtener todos los datos del colaborador logueado (incluida la foto de perfil)
        $colaborador = $colaboradorModel->findById($colaboradorId);

        // Obtener métricas específicas para este colaborador
        $totalEquiposAsignados = $inventarioModel->countAssignedToColaborador($colaboradorId);
        $solicitudesColaboradorPendientes = $necesidadModel->countByEstadoAndColaborador('Solicitado', $colaboradorId);
        $solicitudesColaboradorAprobadas = $necesidadModel->countByEstadoAndColaborador('Aprobado', $colaboradorId);
        $solicitudesColaboradorCompletadas = $necesidadModel->countByEstadoAndColaborador('Completado', $colaboradorId);

        $this->render('Views/portal/index.php', [
            'pageTitle' => 'Portal del Colaborador',
            'colaborador' => $colaborador, // Pasa los datos completos del colaborador
            'totalEquiposAsignados' => $totalEquiposAsignados,
            'solicitudesColaboradorPendientes' => $solicitudesColaboradorPendientes,
            'solicitudesColaboradorAprobadas' => $solicitudesColaboradorAprobadas,
            'solicitudesColaboradorCompletadas' => $solicitudesColaboradorCompletadas,
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
        // Se define un filtro para obtener equipos que estén 'Asignado' O 'En Reparación'.
        $options = [
            'paginate' => false,
            'filters' => [
                'a.colaborador_id' => $colaboradorId,
                'i.estado' => ['IN', 'Asignado', 'En Reparación', 'Dañado']
            ]
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
     * Permite a un colaborador reportar un equipo como 'Dañado'.
     */
    public function reportarDano()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventario_id = (int)($_POST['inventario_id'] ?? 0);
            $colaborador_id = $_SESSION['user_id'] ?? 0;

            $inventarioModel = new Inventario();
            $asignacionModel = new Asignacion();

            // Medida de seguridad CRUCIAL: verificar que el equipo realmente pertenece al colaborador.
            if ($asignacionModel->isEquipoAssignedToColaborador($inventario_id, $colaborador_id)) {

                // 1. Obtenemos todos los datos actuales del equipo.
                $equipo = $inventarioModel->findById($inventario_id);

                if ($equipo) {
                    // 2. Cambiamos únicamente el estado.
                    $equipo['estado'] = 'Dañado';

                    // 3. Guardamos el objeto completo. El método save() se encargará del resto.
                    //    Es importante pasar todos los datos para que no se pierdan.
                    $inventarioModel->save($equipo);
                    $_SESSION['mensaje_sa2'] = ['title' => '¡Reportado!', 'text' => 'Se ha notificado el daño del equipo.', 'icon' => 'success'];
                } else {
                    $_SESSION['mensaje_sa2'] = ['title' => 'Error', 'text' => 'No se encontró el equipo a reportar.', 'icon' => 'error'];
                }

                header('Location: ' . BASE_URL . 'index.php?route=portal&action=misEquipos');
                exit;
            }
        }
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
