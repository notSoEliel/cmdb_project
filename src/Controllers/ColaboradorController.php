<?php

namespace App\Controllers;

use App\Models\Colaborador;

class ColaboradorController extends BaseController{

    public function index() {
        $colaboradorModel = new Colaborador();

        // Lógica de ordenamiento
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        // Preparamos los datos para la vista
        $data = [
            'colaboradores' => $colaboradorModel->findAll(['sort' => $sort, 'order' => $order]),
            'pageTitle' => 'Gestionar Colaboradores',
            'formId' => 'form-colaborador'
        ];

        $this->render('Views/colaboradores/index.php', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaboradorModel = new Colaborador();
            $colaboradorModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Colaborador creado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaboradorModel = new Colaborador();
            $colaboradorModel->save($_POST);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Colaborador actualizado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }

    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colaboradorModel = new Colaborador();
            $colaboradorModel->delete($_POST['id']);

            // Guardamos el mensaje en la sesión
            $_SESSION['mensaje_sa2'] = [
                'title' => '¡Éxito!',
                'text' => 'Colaborador eliminado correctamente.',
                'icon' => 'success'
            ];

            header('Location: ' . BASE_URL . 'index.php?route=colaboradores');
            exit;
        }
    }
}