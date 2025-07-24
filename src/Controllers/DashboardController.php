<?php

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Colaborador;
use App\Models\Categoria;
use App\Models\Necesidad;
use App\Models\Usuario;

class DashboardController extends BaseController
{

    public function index()
    {
        $inventarioModel = new Inventario();
        $colaboradorModel = new Colaborador();
        $categoriaModel = new Categoria();
        $necesidadModel = new Necesidad();
        $usuarioModel = new Usuario();

        // Existing totals (already excluding Donado/En Descarte)
        $totalEquiposActivos = $inventarioModel->countFiltered(); // Esto es el total de equipos activos (no donados/descartados)
        $totalColaboradores = $colaboradorModel->countFiltered();
        $totalCategorias = $categoriaModel->countFiltered();
        $totalAdminUsers = $usuarioModel->countFiltered(); // Obtener el conteo de usuarios administradores


        // Nuevas métricas de inventario detalladas
        $totalInventarioGeneral = $inventarioModel->countAll(); // Todos los equipos, incluyendo donados/descartados
        $totalAsignados = $inventarioModel->countByEstado('Asignado');
        $totalDisponibles = $inventarioModel->countByEstado('En Stock');
        $totalEnReparacionDañado = $inventarioModel->countProblemas(); // Suma 'En Reparación' y 'Dañado'
        $totalDonados = $inventarioModel->countByEstado('Donado');
        $totalDescartados = $inventarioModel->countByEstado('En Descarte');
        $totalExpirados = $inventarioModel->countExpirados();
        $totalPorExpirar = $inventarioModel->countPorExpirar();

        // Métricas de Solicitudes
        $solicitudesPendientes = $necesidadModel->countByEstado('Solicitado');
        $solicitudesAprobadas = $necesidadModel->countByEstado('Aprobado');
        $solicitudesCompletadas = $necesidadModel->countByEstado('Completado');
        $solicitudesRechazadas = $necesidadModel->countByEstado('Rechazado');

        $data = [
            'pageTitle' => 'Dashboard',
            'totalEquiposActivos' => $totalEquiposActivos,
            'totalColaboradores' => $totalColaboradores,
            'totalCategorias' => $totalCategorias,

            'totalInventarioGeneral' => $totalInventarioGeneral,
            'totalAsignados' => $totalAsignados,
            'totalDisponibles' => $totalDisponibles,
            'totalEnReparacionDañado' => $totalEnReparacionDañado,
            'totalDonados' => $totalDonados,
            'totalDescartados' => $totalDescartados,
            'totalExpirados' => $totalExpirados,
            'totalPorExpirar' => $totalPorExpirar,

            'solicitudesPendientes' => $solicitudesPendientes,
            'solicitudesAprobadas' => $solicitudesAprobadas,
            'solicitudesCompletadas' => $solicitudesCompletadas,
            'solicitudesRechazadas' => $solicitudesRechazadas,
            'totalAdminUsers' => $totalAdminUsers, // Pasar el conteo a la vista

        ];

        $this->render('Views/dashboard/index.php', $data);
    }
}
