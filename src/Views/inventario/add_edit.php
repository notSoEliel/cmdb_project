<?php

/**
 * Vista para el formulario de añadir/editar equipos.
 *
 * Este archivo es responsable de:
 * 1. Mostrar el formulario para añadir un solo equipo.
 * 2. Mostrar el formulario para añadir múltiples equipos por lote.
 * 3. Pre-llenar el formulario si se está en modo edición.
 * 4. Mostrar las opciones de selección si no se ha especificado un formulario.
 *
 * Variables esperadas del controlador (InventarioController::showAddForm()):
 * - $pageTitle: Título de la página.
 * - $formIds: IDs de los formularios para la validación (array).
 * - $categorias: Lista de categorías para los selectores.
 * - $equipoActual: Array de datos del equipo si se está editando (null si es nuevo).
 * - $isEditing: Booleano que indica si se está en modo edición.
 * - $showBatchForm: Booleano que indica si se debe mostrar el formulario por lote.
 * - $suggestedNextSerialNumber: El próximo número de serie sugerido para lotes.
 */

// Estas variables son generadas por el controlador InventarioController::showAddForm()
// Se utilizan para determinar qué sección de la vista se debe mostrar.

// La sección de opciones de selección (añadir individual o por lote)
// solo se muestra si NO estamos editando Y NO se ha solicitado una acción de formulario específica.
$showOptions = !$isEditing && !isset($_GET['form_action']);

// El formulario individual se muestra si estamos editando UN equipo
// O si la acción de formulario solicitada es 'single'.
$showSingleForm = $isEditing || (isset($_GET['form_action']) && $_GET['form_action'] === 'single');

// El formulario por lote se muestra si la acción de formulario solicitada es 'batch'.
$showBatchFormOnly = isset($_GET['form_action']) && $_GET['form_action'] === 'batch';

?>

<a href="index.php?route=inventario" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Inventario</a>

<h1 class="mt-2"><?= $pageTitle ?></h1>

<?php if ($showOptions) : // Mostrar opciones de selección si no hay formulario activo 
?>
    <div class="row mt-4 mb-4" id="form-selection-options">
        <div class="col-md-6">
            <div class="card text-center h-100 p-4 option-card">
                <div class="card-body">
                    <i class="bi bi-box-fill display-1 text-primary mb-3"></i>
                    <h4 class="card-title">Ingresar un Solo Equipo</h4>
                    <p class="card-text text-muted">Añade un equipo al inventario con todos sus detalles.</p>
                    <a href="index.php?route=inventario&action=showAddForm&form_action=single" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle-fill me-2"></i>Añadir Equipo Individual
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center h-100 p-4 option-card">
                <div class="card-body">
                    <i class="bi bi-boxes display-1 text-info mb-3"></i>
                    <h4 class="card-title">Ingresar Equipos por Lote</h4>
                    <p class="card-text text-muted">Añade múltiples equipos del mismo tipo con números de serie incrementales.</p>
                    <a href="index.php?route=inventario&action=showAddForm&form_action=batch" class="btn btn-info mt-3">
                        <i class="bi bi-grid-fill me-2"></i>Añadir por Lote
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($showSingleForm) : // Formulario para un solo equipo 
?>
    <div class="card mb-4" id="single-equipment-form">
        <div class="card-header">
            <?= $equipoActual ? '✏️ Editando Equipo: ' . htmlspecialchars($equipoActual['nombre_equipo']) : '➕ Agregar Nuevo Equipo' ?>
            <?php if (!$isEditing): // Mostrar botón de volver solo si no estamos editando 
            ?>
                <a href="index.php?route=inventario&action=showAddForm" class="btn btn-secondary btn-sm float-end"><i class="bi bi-arrow-left"></i> Volver a Opciones</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form id="form-inventario" action="index.php?route=inventario&action=<?= $equipoActual ? 'update' : 'store' ?>" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($equipoActual['id'] ?? '') ?>">
                <div class="row g-3">

                    <div class="col-md-8 form-group">
                        <label class="form-label" for="nombre_equipo">Nombre del Equipo</label>
                        <input id="nombre_equipo" type="text" class="form-control" name="nombre_equipo" value="<?= htmlspecialchars($equipoActual['nombre_equipo'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label class="form-label" for="categoria_id">Categoría</label>
                        <select id="categoria_id" class="form-select" name="categoria_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= (isset($equipoActual) && $equipoActual['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label class="form-label" for="marca">Marca</label>
                        <input id="marca" type="text" class="form-control" name="marca" value="<?= htmlspecialchars($equipoActual['marca'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="modelo">Modelo</label>
                        <input id="modelo" type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($equipoActual['modelo'] ?? '') ?>" required></div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="serie">No. Serie</label>
                        <input id="serie" type="text" class="form-control" name="serie" value="<?= htmlspecialchars($equipoActual['serie'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="costo">Costo ($)</label>
                        <input id="costo" type="number" min="0" step="0.01" class="form-control" name="costo" value="<?= htmlspecialchars($equipoActual['costo'] ?? '0.00') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="fecha_ingreso">Fecha de Ingreso</label>
                        <input id="fecha_ingreso" type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($equipoActual['fecha_ingreso'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="tiempo_depreciacion_anios">Depreciación (Años)</label>
                        <input id="tiempo_depreciacion_anios" type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="<?= htmlspecialchars($equipoActual['tiempo_depreciacion_anios'] ?? '0') ?>" required>
                    </div>

                    <?php if ($equipoActual) : // Solo mostrar estado y notas de donación/descarte en modo edición 
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
                    <?php if ($equipoActual): ?>
                        <a href="index.php?route=inventario" class="btn btn-secondary ms-2">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($showBatchFormOnly) : // Formulario para equipos por lote 
?>
    <div class="card mb-4" id="batch-equipment-form">
        <div class="card-header">
            ➕ Agregar Nuevos Equipos por Lote
            <a href="index.php?route=inventario&action=showAddForm" class="btn btn-secondary btn-sm float-end"><i class="bi bi-arrow-left"></i> Volver a Opciones</a>
        </div>
        <div class="card-body">
            <form id="form-inventario-lote" method="POST" action="index.php?route=inventario&action=batchStore">
                <div class="row g-3">
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="cantidad">Cantidad de Equipos</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="prefijo_serie">Prefijo de Serie (Opcional)</label>
                        <input type="text" class="form-control" id="prefijo_serie" name="prefijo_serie" placeholder="Ej: LAPTOP-">
                        <small class="form-text text-muted">Se añadirá antes del número incremental (Ej: LAPTOP-0001).</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="numero_inicio_serie">Número Inicial de Serie</label>
                        <input type="number" class="form-control" id="numero_inicio_serie" name="numero_inicio_serie" min="0" value="<?= htmlspecialchars($suggestedNextSerialNumber) ?>" required>
                        <small class="form-text text-muted" id="current-suggested-serial">Sugerido: <?= htmlspecialchars($suggestedNextSerialNumber) ?></small>
                    </div>
                </div>

                <hr class="my-4">
                <h5>Datos Comunes para el Lote</h5>
                <div class="row g-3">
                    <div class="col-md-8 form-group">
                        <label class="form-label" for="nombre_equipo_lote">Nombre del Equipo</label>
                        <input id="nombre_equipo_lote" type="text" class="form-control" name="nombre_equipo" placeholder="Ej: Laptop Dell Latitude" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="categoria_id_lote">Categoría</label>
                        <select id="categoria_id_lote" class="form-select" name="categoria_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $cat) : ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="marca_lote">Marca</label>
                        <input id="marca_lote" type="text" class="form-control" name="marca" placeholder="Ej: Dell" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="modelo_lote">Modelo</label>
                        <input id="modelo_lote" type="text" class="form-control" name="modelo" placeholder="Ej: Latitude 7420" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="costo_lote">Costo ($)</label>
                        <input id="costo_lote" type="number" min="0" step="0.01" class="form-control" name="costo" value="0.00" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="fecha_ingreso_lote">Fecha de Ingreso</label>
                        <input id="fecha_ingreso_lote" type="date" class="form-control" name="fecha_ingreso" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label" for="tiempo_depreciacion_anios_lote">Depreciación (Años)</label>
                        <input id="tiempo_depreciacion_anios_lote" type="number" min="0" class="form-control" name="tiempo_depreciacion_anios" value="0" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-info" type="submit">Añadir Equipos por Lote</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
    function toggleDonacion(estado) {
        const wrapper = document.getElementById('notas_donacion_wrapper');
        if (wrapper) { // Comprueba si el elemento existe
            wrapper.style.display = (estado === 'Donado' || estado === 'En Descarte') ? 'block' : 'none';
        }
    }
    // Ejecutar al cargar la página para establecer el estado inicial (solo para edición individual)
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