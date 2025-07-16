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
    <div class="card-header"><?= $equipoActual ? '✏️ Editando Equipo: ' . htmlspecialchars($equipoActual['nombre_equipo']) : '➕ Agregar Nuevo Equipo' ?></div>
    <div class="card-body">
        <form id="form-inventario" action="index.php?route=inventario&action=<?= $equipoActual ? 'update' : 'store' ?>" method="POST">
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
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($equipoActual) && $equipoActual['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 form-group"><label class="form-label" for="marca">Marca</label><input id="marca" type="text" class="form-control" name="marca" value="<?= htmlspecialchars($equipoActual['marca'] ?? '') ?>"></div>
                <div class="col-md-4 form-group"><label class="form-label" for="modelo">Modelo</label><input id="modelo" type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($equipoActual['modelo'] ?? '') ?>"></div>
                <div class="col-md-4 form-group"><label class="form-label" for="serie">No. Serie</label><input id="serie" type="text" class="form-control" name="serie" value="<?= htmlspecialchars($equipoActual['serie'] ?? '') ?>"></div>
                <div class="col-md-4 form-group"><label class="form-label" for="costo">Costo ($)</label><input id="costo" type="number" min="0" step="0.01" class="form-control" name="costo" value="<?= htmlspecialchars($equipoActual['costo'] ?? '0.00') ?>"></div>
                <div class="col-md-4 form-group"><label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label><input id="fecha_ingreso" type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($equipoActual['fecha_ingreso'] ?? '') ?>"></div>
                <div class="col-md-4 form-group"><label class="form-label" for="tiempo_depreciacion_anios">Depreciación (Años)</label><input id="tiempo_depreciacion_anios" type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="<?= htmlspecialchars($equipoActual['tiempo_depreciacion_anios'] ?? '0') ?>"></div>

                <?php if ($equipoActual) : ?>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="estado">Cambiar Estado</label>
                        <select name="estado" id="estado" class="form-select" onchange="toggleDonacion(this.value)">
                            <?php
                            // Guardamos el estado actual para no perderlo si no se cambia
                            echo '<option value="' . htmlspecialchars($equipoActual['estado']) . '" selected>' . htmlspecialchars($equipoActual['estado']) . ' (Actual)</option>';

                            // Definimos los únicos estados que se pueden seleccionar manualmente
                            $estadosPermitidos = ['En Reparación', 'Dañado', 'En Descarte', 'Donado'];

                            foreach ($estadosPermitidos as $estado) {
                                // Solo mostramos la opción si es diferente al estado actual
                                if ($estado !== $equipoActual['estado']) {
                                    echo '<option value="' . $estado . '">' . $estado . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-8 form-group" id="notas_donacion_wrapper" style="display: none;">
                        <label class="form-label" for="notas_donacion">Notas de Donación / Descarte</label>
                        <textarea name="notas_donacion" id="notas_donacion" class="form-control" rows="2" placeholder="Añade detalles como el responsable, la fecha, o la razón..."><?= htmlspecialchars($equipoActual['notas_donacion'] ?? '') ?></textarea>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <?php if ($equipoActual): ?>
                    <a href="index.php?route=inventario" class="btn btn-secondary ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="notesModalLabel">Notas de Donación / Descarte</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="notesModalBody">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
    function toggleDonacion(estado) {
        const wrapper = document.getElementById('notas_donacion_wrapper');
        if (wrapper) { // Comprueba si el elemento existe
            wrapper.style.display = (estado === 'Donado' || estado === 'En Descarte') ? 'block' : 'none';
        }
    }
    // Ejecutar al cargar la página para establecer el estado inicial
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect) {
            toggleDonacion(estadoSelect.value);
        }
    });
</script>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>