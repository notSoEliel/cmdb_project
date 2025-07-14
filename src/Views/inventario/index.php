<?php
$inventarioModel = new \App\Models\Inventario();
$equipoActual = isset($_GET['editar_id']) ? $inventarioModel->findById($_GET['editar_id']) : null;
$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'asc';
$nextOrder = ($order === 'asc') ? 'desc' : 'asc';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container mt-4">
        <a href="index.php">← Volver al Menú</a>
        <h1 class="mt-2">Gestión de Inventario</h1>

        <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header"><?= $equipoActual ? '✏️ Editando Equipo' : '➕ Agregar Nuevo Equipo' ?></div>
            <div class="card-body">
                <form id="form-validation" action="index.php?route=inventario&action=<?= $equipoActual ? 'update' : 'store' ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $equipoActual['id'] ?? '' ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre del Equipo</label>
                            <input type="text" class="form-control" name="nombre_equipo" value="<?= htmlspecialchars($equipoActual['nombre_equipo'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($equipoActual) && $equipoActual['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?= htmlspecialchars($equipoActual['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($equipoActual['modelo'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Serie</label>
                            <input type="text" class="form-control" name="serie" value="<?= htmlspecialchars($equipoActual['serie'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Costo ($)</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="costo" value="<?= htmlspecialchars($equipoActual['costo'] ?? '0.00') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($equipoActual['fecha_ingreso'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Depreciación (Años)</label>
                            <input type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="<?= htmlspecialchars($equipoActual['tiempo_depreciacion_anios'] ?? '0') ?>">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Guardar Equipo</button>
                        <?php if ($equipoActual): ?>
                            <a href="index.php?route=inventario" class="btn btn-secondary ms-2">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Inventario Actual</div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th><a href="?route=inventario&sort=id&order=<?= ($sort === 'id') ? $nextOrder : 'asc' ?>">ID</a></th>
                            <th><a href="?route=inventario&sort=nombre_equipo&order=<?= ($sort === 'nombre_equipo') ? $nextOrder : 'asc' ?>">Equipo</a></th>
                            <th>Categoría</th>
                            <th><a href="?route=inventario&sort=marca&order=<?= ($sort === 'marca') ? $nextOrder : 'asc' ?>">Marca</a></th>
                            <th><a href="?route=inventario&sort=serie&order=<?= ($sort === 'serie') ? $nextOrder : 'asc' ?>">Serie</a></th>
                            <th>Estado</th>
                            <th class="text-center">Imágenes</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventarios as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars($item['nombre_equipo']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($item['nombre_categoria']) ?></span></td>
                                <td><?= htmlspecialchars($item['marca']) ?></td>
                                <td><?= htmlspecialchars($item['serie']) ?></td>
                                <td><?= htmlspecialchars($item['estado']) ?></td>
                                <td class="text-center"> <a href="?route=inventario&action=showImages&id=<?= $item['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-images"></i>
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="?route=inventario&editar_id=<?= $item['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="index.php?route=inventario&action=destroy" method="POST" class="d-inline form-delete"><input type="hidden" name="id" value="<?= $item['id'] ?>"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i></button></form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>