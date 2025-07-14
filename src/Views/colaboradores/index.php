<?php
$colaboradorModel = new \App\Models\Colaborador();
$colaboradorActual = isset($_GET['editar_id']) ? $colaboradorModel->findById($_GET['editar_id']) : null;
$mensaje = isset($_GET['mensaje']) ? htmlspecialchars($_GET['mensaje']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'asc';
$nextOrder = ($order === 'asc') ? 'desc' : 'asc';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Colaboradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container mt-5">
        <a href="index.php">← Volver al Menú Principal</a>
        <h1 class="mt-2">Gestión de Colaboradores (MVC)</h1>
        <p>Administra los colaboradores que usarán los equipos del inventario.</p>

        <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header"><?= $colaboradorActual ? '✏️ Editando Colaborador' : '➕ Agregar Nuevo Colaborador' ?></div>
            <div class="card-body">
                <form id="form-colaborador" action="index.php?route=colaboradores&action=<?= $colaboradorActual ? 'update' : 'store' ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $colaboradorActual['id'] ?? '' ?>">
                    <div class="row g-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control " name="nombre" value="<?= htmlspecialchars($colaboradorActual['nombre'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control " name="apellido" value="<?= htmlspecialchars($colaboradorActual['apellido'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">ID Único</label>
                            <input type="text" class="form-control" name="identificacion_unica" value="<?= htmlspecialchars($colaboradorActual['identificacion_unica'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($colaboradorActual['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Ubicación</label>
                            <input type="text" class="form-control" name="ubicacion" value="<?= htmlspecialchars($colaboradorActual['ubicacion'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" placeholder="Formato: 6123-4567 o 213-4567" value="<?= htmlspecialchars($colaboradorActual['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" placeholder="<?= $colaboradorActual ? 'Dejar en blanco para no cambiar' : '' ?>" <?= $colaboradorActual ? '' : '' ?>>
                            <small class="form-text text-muted"><?= $colaboradorActual ? 'Solo ingresa una nueva contraseña si deseas cambiarla.' : '' ?></small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Guardar Colaborador</button>
                        <?php if ($colaboradorActual): ?>
                            <a href="index.php?route=colaboradores" class="btn btn-secondary ms-2">Cancelar Edición</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Lista de Colaboradores</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?route=colaboradores&sort=id&order=<?= ($sort === 'id') ? $nextOrder : 'asc' ?>">ID <?= ($sort === 'id') ? ($order === 'asc' ? '▲' : '▼') : '' ?></a></th>
                            <th><a href="?route=colaboradores&sort=nombre&order=<?= ($sort === 'nombre') ? $nextOrder : 'asc' ?>">Nombre <?= ($sort === 'nombre') ? ($order === 'asc' ? '▲' : '▼') : '' ?></a></th>
                            <th><a href="?route=colaboradores&sort=email&order=<?= ($sort === 'email') ? $nextOrder : 'asc' ?>">Email <?= ($sort === 'email') ? ($order === 'asc' ? '▲' : '▼') : '' ?></a></th>
                            <th>Ubicación</th>
                            <th>Teléfono</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($colaboradores)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay colaboradores registrados.</td>
                            </tr>
                            <?php else: foreach ($colaboradores as $colaborador): ?>
                                <tr>
                                    <td><?= $colaborador['id'] ?></td>
                                    <td><?= htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?></td>
                                    <td><?= htmlspecialchars($colaborador['email']) ?></td>
                                    <td><?= htmlspecialchars($colaborador['ubicacion']) ?></td> 
                                    <td><?= htmlspecialchars($colaborador['telefono']) ?></td>
                                    <td class="text-end">
                                        <a href="?route=colaboradores&editar_id=<?= $colaborador['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="index.php?route=colaboradores&action=destroy" method="POST" class="d-inline form-delete"><input type="hidden" name="id" value="<?= $colaborador['id'] ?>"><button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i></button></form>
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