<?php
namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Colaborador;
use App\Models\Categoria;

class DashboardController extends BaseController {

    public function index() {
        // Obtenemos los totales de cada modelo
        $inventarioModel = new Inventario();
        $colaboradorModel = new Colaborador();
        $categoriaModel = new Categoria();

        $data = [
            // AHORA: Llamamos a countFiltered() sin opciones para obtener el total.
            'totalEquipos' => $inventarioModel->countFiltered(),
            'totalColaboradores' => $colaboradorModel->countFiltered(),
            'totalCategorias' => $categoriaModel->countFiltered(),
            'pageTitle' => 'Dashboard'
        ];

        $this->render('Views/dashboard/index.php', $data);
    }
}