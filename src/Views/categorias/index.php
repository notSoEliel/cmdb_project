<?php
// Preparar variables para la vista
$categoriaModel = new \App\Models\Categoria(); // Necesario para buscar la categoría a editar
$categoriaActual = isset($_GET['editar_id']) ? $categoriaModel->findById($_GET['editar_id']) : null;
$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'asc';
$nextOrder = ($order === 'asc') ? 'desc' : 'asc';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Gestión de Categorías (MVC)</h1>
        <p>Aquí puedes administrar las categorías para el inventario. Haz clic en los encabezados de la tabla para ordenar.</p>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $mensaje ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header"><?= $categoriaActual ? '✏️ Editando Categoría' : '➕ Agregar Nueva Categoría' ?></div>
            <div class="card-body">
                <form action="index.php?route=categorias&action=<?= $categoriaActual ? 'update' : 'store' ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $categoriaActual['id'] ?? '' ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre de la categoría" value="<?= htmlspecialchars($categoriaActual['nombre'] ?? '') ?>" required>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                        <?php if ($categoriaActual): ?>
                            <a href="index.php?route=categorias&action=index" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Lista de Categorías</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?route=categorias&action=index&sort=id&order=<?= ($sort === 'id') ? $nextOrder : 'asc' ?>">ID <?php if ($sort === 'id') echo ($order === 'asc') ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?></a></th>
                            <th><a href="?route=categorias&action=index&sort=nombre&order=<?= ($sort === 'nombre') ? $nextOrder : 'asc' ?>">Nombre <?php if ($sort === 'nombre') echo ($order === 'asc') ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?></a></th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No hay categorías registradas.</td>
                            </tr>
                            <?php else: foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td><?= $categoria['id'] ?></td>
                                    <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                                    <td class="text-end">
                                        <a href="?route=categorias&action=index&editar_id=<?= $categoria['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Editar</a>
                                        <form action="index.php?route=categorias&action=destroy" method="POST" class="d-inline form-delete">
                                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>