<?php
// Lógica para obtener los datos del usuario que se está editando
$usuarioActual = null;
if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
    $usuarioModel = new \App\Models\Usuario();
    $usuarioActual = $usuarioModel->findById((int)$_GET['editar_id']);
}
?>

<h1 class="mb-4"><?= $pageTitle ?? 'Gestionar Usuarios' ?></h1>

<div class="card mb-4">
    <div class="card-header"><?= $usuarioActual ? '✏️ Editando Usuario Admin' : '➕ Agregar Nuevo Usuario Admin' ?></div>
    <div class="card-body">
        <form id="form-usuario" action="index.php?route=usuarios&action=<?= $usuarioActual ? 'update' : 'store' ?>" method="POST">
            <input type="hidden" name="id" value="<?= $usuarioActual['id'] ?? '' ?>">
            <div class="row g-3">
                <div class="col-md-6 form-group">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input id="nombre" type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuarioActual['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuarioActual['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-group">
                        <input id="password" type="password" class="form-control" name="password" placeholder="<?= $usuarioActual ? 'Dejar en blanco para no cambiar' : '' ?>">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <small class="form-text text-muted"><?= !$usuarioActual ? 'La contraseña es obligatoria al crear.' : '' ?></small>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="activo">Estado</label>
                    <select name="activo" id="activo" class="form-select">
                        <option value="1" <?= (isset($usuarioActual) && $usuarioActual['activo'] == 1) ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= (isset($usuarioActual) && $usuarioActual['activo'] == 0) ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Guardar Usuario</button>
                <?php if ($usuarioActual): ?>
                    <a href="index.php?route=usuarios" class="btn btn-secondary ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>