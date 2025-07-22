<?php

namespace App\Controllers;

use App\Models\Usuario;

class UsuarioController extends BaseController
{
    public function index()
    {
        $usuarioModel = new Usuario();
        $options = $_GET;
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['perPage'] ?? 10);

        $data = $usuarioModel->findAll($options);
        $totalRecords = $usuarioModel->countFiltered($options);
        $totalPages = ceil($totalRecords / $perPage);

        $tableConfig = [
            'columns' => [
                ['header' => 'ID', 'field' => 'id', 'sort_by' => 'u.id'],
                ['header' => 'Nombre', 'field' => 'nombre', 'sort_by' => 'u.nombre'],
                ['header' => 'Email', 'field' => 'email', 'sort_by' => 'u.email'],
                ['header' => 'Estado', 'field' => 'activo', 'sort_by' => 'u.activo'],
            ],
            'data' => $data,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'id',
                'order' => $_GET['order'] ?? 'asc',
                'filters' => []
            ],
            // Para los usuarios, solo queremos el botón de editar.
            'actions' => [
                'edit_action' => 'index',
            ]
        ];

        $this->render('Views/admin/usuarios/index.php', [
            'pageTitle' => 'Gestionar Usuarios Admin',
            'formId' => 'form-usuario',
            'tableConfig' => $tableConfig
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                (new Usuario())->save($_POST);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Usuario creado.', 'icon' => 'success'];
            } catch (\Exception $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=usuarios');
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                (new Usuario())->save($_POST);
                $_SESSION['mensaje_sa2'] = ['title' => '¡Éxito!', 'text' => 'Usuario actualizado.', 'icon' => 'success'];
            } catch (\Exception $e) {
                $_SESSION['mensaje_sa2'] = ['title' => '¡Error!', 'text' => $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: ' . BASE_URL . 'index.php?route=usuarios');
            exit;
        }
    }
}
