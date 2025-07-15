<?php
/**
 * Vista principal para la Gestión de Inventario.
 *
 * Este archivo es responsable de dos cosas:
 * 1. Mostrar el formulario para agregar o editar un equipo.
 * 2. Incluir el componente de tabla dinámica que mostrará la lista de equipos con búsqueda, filtros y paginación.
 *
 * El controlador (InventarioController) ya ha preparado todas las variables necesarias:
 * - $categorias: Para el menú desplegable del formulario.
 * - $tableConfig: Un array con toda la configuración para la tabla dinámica.
 * - $pageTitle: El título de la página.
 * - $formId: El ID para el formulario para activar la validación.
 */

// Se busca si estamos en modo edición para pre-llenar el formulario.
$equipoActual = null;
if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
    $inventarioModel = new \App\Models\Inventario(); // Se instancia solo si es necesario
    $equipoActual = $inventarioModel->findById((int)$_GET['editar_id']);
}
?>

<a href="index.php">← Volver al Menú</a>
<h1 class="mt-2"><?= $pageTitle ?></h1>

<div class="card mb-4">
    <div class="card-header"><?= $equipoActual ? '✏️ Editando Equipo' : '➕ Agregar Nuevo Equipo' ?></div>
    <div class="card-body">
        <form id="<?= $formId ?>" action="index.php?route=inventario&action=<?= $equipoActual ? 'update' : 'store' ?>" method="POST">
            <input type="hidden" name="id" value="<?= $equipoActual['id'] ?? '' ?>">
            <div class="row g-3">
                <div class="col-md-8 form-group">
                    <label class="form-label" for="nombre_equipo">Nombre del Equipo</label>
                    <input id="nombre_equipo" type="text" class="form-control" name="nombre_equipo" value="<?= htmlspecialchars($equipoActual['nombre_equipo'] ?? '') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="categoria_id">Categoría</label>
                    <select id="categoria_id" class="form-select" name="categoria_id">
                        <option value="">Seleccione...</option>
                        <?php foreach ($categorias as $cat) : ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($equipoActual) && $equipoActual['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="marca">Marca</label>
                    <input id="marca" type="text" class="form-control" name="marca" value="<?= htmlspecialchars($equipoActual['marca'] ?? '') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="modelo">Modelo</label>
                    <input id="modelo" type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($equipoActual['modelo'] ?? '') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="serie">No. Serie</label>
                    <input id="serie" type="text" class="form-control" name="serie" value="<?= htmlspecialchars($equipoActual['serie'] ?? '') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="costo">Costo ($)</label>
                    <input id="costo" type="number" min="0" step="0.01" class="form-control" name="costo" value="<?= htmlspecialchars($equipoActual['costo'] ?? '0.00') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label>
                    <input id="fecha_ingreso" type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($equipoActual['fecha_ingreso'] ?? '') ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="tiempo_depreciacion_anios">Depreciación (Años)</label>
                    <input id="tiempo_depreciacion_anios" type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="<?= htmlspecialchars($equipoActual['tiempo_depreciacion_anios'] ?? '0') ?>">
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Guardar Equipo</button>
                <?php if ($equipoActual) : ?>
                    <a href="index.php?route=inventario" class="btn btn-secondary ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>