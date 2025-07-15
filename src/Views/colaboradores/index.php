<?php

/**
 * Vista principal para la Gestión de Colaboradores.
 *
 * Muestra el formulario para agregar/editar y carga el componente de tabla dinámica.
 * Todas las variables necesarias ($tableConfig, $pageTitle, etc.) son preparadas
 * por el ColaboradorController.
 */

// Lógica para obtener los datos del colaborador que se está editando
// para poder rellenar el formulario.
$colaboradorActual = null;
if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
    // Instanciamos el modelo solo si es necesario
    $colaboradorModel = new \App\Models\Colaborador();
    $colaboradorActual = $colaboradorModel->findById((int)$_GET['editar_id']);
}
?>

<a href="index.php">← Volver al Menú Principal</a>
<h1 class="mt-2"><?= $pageTitle ?? 'Gestionar Colaboradores' ?></h1>
<p>Administra los colaboradores que usarán los equipos del inventario.</p>

<div class="card mb-4">
    <div class="card-header"><?= $colaboradorActual ? '✏️ Editando Colaborador' : '➕ Agregar Nuevo Colaborador' ?></div>
    <div class="card-body">
        <form id="<?= $formId ?>" action="index.php?route=colaboradores&action=<?= $colaboradorActual ? 'update' : 'store' ?>" method="POST">
            <input type="hidden" name="id" value="<?= $colaboradorActual['id'] ?? '' ?>">
            <div class="row g-3">
                <div class="col-md-6 form-group">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input id="nombre" type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($colaboradorActual['nombre'] ?? '') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="apellido">Apellido</label>
                    <input id="apellido" type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($colaboradorActual['apellido'] ?? '') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="identificacion_unica">ID Único</label>
                    <input id="identificacion_unica" type="text" class="form-control" name="identificacion_unica" value="<?= htmlspecialchars($colaboradorActual['identificacion_unica'] ?? '') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="<?= htmlspecialchars($colaboradorActual['email'] ?? '') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="ubicacion">Ubicación</label>
                    <input id="ubicacion" type="text" class="form-control" name="ubicacion" value="<?= htmlspecialchars($colaboradorActual['ubicacion'] ?? '') ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input id="telefono" type="tel" class="form-control" name="telefono" placeholder="Formato: 6123-4567" value="<?= htmlspecialchars($colaboradorActual['telefono'] ?? '') ?>">
                </div>
                <div class="col-md-12 form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-group">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Dejar en blanco para no cambiar">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill" id="togglePasswordIcon"></i>
                        </button>
                    </div>
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

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>