<?php

/**
 * Vista para el formulario de añadir/editar equipos (UNIFICADO).
 *
 * Este archivo es responsable de:
 * 1. Mostrar un formulario unificado para añadir un nuevo equipo (individual o por lote)
 * o para editar un equipo existente.
 * 2. Pre-llenar el formulario si se está en modo edición.
 *
 * Variables esperadas del controlador (InventarioController::showAddForm()):
 * - $pageTitle: Título de la página.
 * - $formIds: IDs de los formularios para la validación (array).
 * - $categorias: Lista de categorías para los selectores.
 * - $equipoActual: Array de datos del equipo si se está editando (null si es nuevo).
 * - $isEditing: Booleano que indica si se está en modo edición.
 * - $suggestedNextSerialNumber: El próximo número de serie sugerido para lotes o individual.
 */
?>

<a href="index.php?route=inventario" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Inventario</a>

<h1 class="mt-2"><?= $pageTitle ?></h1>

<div class="card mb-4" id="form-inventario">
    <div class="card-header">
        <?= $isEditing ? '✏️ Editando Equipo: ' . htmlspecialchars($equipoActual['nombre_equipo']) : '➕ Añadir Nuevo Equipo' ?>
        <?php // El botón "Volver a Opciones" ya no es necesario aquí. 
        ?>
    </div>
    <div class="card-body">
        <form id="form-inventario-form" method="POST" action="index.php?route=inventario&action=store">
            <input type="hidden" name="id" value="<?= htmlspecialchars($equipoActual['id'] ?? '') ?>">

            <div class="row g-3">
                <div class="col-md-4 form-group">
                    <label class="form-label" for="cantidad">Cantidad de Equipos</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="<?= $isEditing ? 1 : 1 ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="prefijo_serie">Prefijo de Serie (Opcional)</label>
                    <input type="text" class="form-control" id="prefijo_serie" name="prefijo_serie" placeholder="Ej: LAPTOP-" value="<?= htmlspecialchars($equipoActual['prefijo_serie'] ?? '') ?>">
                    <small class="form-text text-muted">Se añadirá antes del número incremental (Ej: LAPTOP-0001).</small>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="numero_inicio_serie">Número Inicial de Serie</label>
                    <input type="number" class="form-control" id="numero_inicio_serie" name="numero_inicio_serie" min="0" value="<?= htmlspecialchars($equipoActual['numero_final_serie'] ?? $suggestedNextSerialNumber) ?>" required>
                    <small class="form-text text-muted" id="current-suggested-serial">Sugerido: <?= htmlspecialchars($equipoActual['numero_final_serie'] ?? $suggestedNextSerialNumber) ?></small>
                </div>
            </div>

            <hr class="my-4">
            <h5>Datos Comunes para el Equipo</h5>
            <div class="row g-3">
                <div class="col-md-8 form-group">
                    <label class="form-label" for="nombre_equipo_lote">Nombre del Equipo</label>
                    <input id="nombre_equipo_lote" type="text" class="form-control" name="nombre_equipo" placeholder="Ej: Laptop Dell Latitude" value="<?= htmlspecialchars($equipoActual['nombre_equipo'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="categoria_id_lote">Categoría</label>
                    <select id="categoria_id_lote" class="form-select" name="categoria_id" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $cat) : ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>" <?= (isset($equipoActual) && $equipoActual['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="marca_lote">Marca</label>
                    <input id="marca_lote" type="text" class="form-control" name="marca" placeholder="Ej: Dell" value="<?= htmlspecialchars($equipoActual['marca'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="modelo_lote">Modelo</label>
                    <input id="modelo_lote" type="text" class="form-control" name="modelo" placeholder="Ej: Latitude 7420" value="<?= htmlspecialchars($equipoActual['modelo'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="costo_lote">Costo ($)</label>
                    <input id="costo_lote" type="number" min="0" step="0.01" class="form-control" name="costo" value="<?= htmlspecialchars($equipoActual['costo'] ?? '0.00') ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="fecha_ingreso_lote">Fecha de Ingreso</label>
                    <input id="fecha_ingreso_lote" type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($equipoActual['fecha_ingreso'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label" for="tiempo_depreciacion_anios_lote">Depreciación (Años)</label>
                    <input id="tiempo_depreciacion_anios_lote" type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="<?= htmlspecialchars($equipoActual['tiempo_depreciacion_anios'] ?? '0') ?>" required>
                </div>

                <?php if (!$isEditing): // Campo para subir miniatura, solo visible en modo de creación
                ?>
                    <div class="col-md-6 form-group">
                        <label class="form-label" for="imagen_miniatura">Miniatura del Equipo (Obligatoria para lotes)</label>
                        <input id="imagen_miniatura" type="file" class="form-control" name="imagen_miniatura" accept="image/png, image/jpeg, image/gif">
                        <small class="form-text text-muted">Se usará como imagen principal del equipo.</small>
                    </div>
                <?php endif;?>

                <?php if ($isEditing) : // Solo mostrar estado y notas de donación/descarte en modo edición
                ?>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="estado">Cambiar Estado</label>
                        <select name="estado" id="estado" class="form-select" onchange="toggleDonacion(this.value)">
                            <?php
                            $estadoActual = $equipoActual['estado'] ?? '';
                            echo '<option value="' . htmlspecialchars($estadoActual) . '" selected>' . htmlspecialchars($estadoActual) . ' (Actual)</option>';
                            $estadosPermitidos = ['En Reparación', 'Dañado', 'En Descarte', 'Donado', 'Disponible', 'Asignado'];
                            foreach ($estadosPermitidos as $estado) {
                                if ($estado !== $estadoActual) {
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
                <?php if ($isEditing) : ?>
                    <a href="index.php?route=inventario" class="btn btn-secondary ms-2">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleDonacion(estado) {
        const wrapper = document.getElementById('notas_donacion_wrapper');
        if (wrapper) { // Comprueba si el elemento existe
            wrapper.style.display = (estado === 'Donado' || estado === 'En Descarte') ? 'block' : 'none';
        }
    }
    // Ejecutar al cargar la página para establecer el estado inicial (solo para edición)
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect) {
            toggleDonacion(estadoSelect.value);
        }

        // Lógica para la sugerencia automática del número de serie en el formulario de lote
        const prefijoSerieInput = document.getElementById('prefijo_serie');
        const numeroInicioSerieInput = document.getElementById('numero_inicio_serie');
        const suggestedSerialText = document.getElementById('current-suggested-serial');

        if (prefijoSerieInput && numeroInicioSerieInput) {
            // Función para obtener la sugerencia del número de serie
            const getSuggestedSerialNumber = async (prefix) => {
                // Hacer una llamada AJAX a tu controlador
                const response = await fetch(`index.php?route=inventario&action=showAddForm&form_action=batch&prefijo_serie_sug=${encodeURIComponent(prefix)}`);
                const html = await response.text(); // Obtener el HTML completo de la respuesta

                // Crear un DOM temporal para analizar el HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Extraer el valor del input 'numero_inicio_serie' del DOM temporal
                const newSuggestedValue = tempDiv.querySelector('#numero_inicio_serie').value;
                return newSuggestedValue;
            };

            // Event listener para el cambio en el prefijo de serie
            prefijoSerieInput.addEventListener('input', async () => {
                const prefix = prefijoSerieInput.value;
                const newSuggestedNum = await getSuggestedSerialNumber(prefix);
                numeroInicioSerieInput.value = newSuggestedNum;
                if (suggestedSerialText) {
                    suggestedSerialText.textContent = `Sugerido: ${newSuggestedNum}`;
                }
            });
        }
    });
</script>