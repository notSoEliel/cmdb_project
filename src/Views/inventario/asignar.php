<?php
/**
 * Vista para Asignar un Equipo.
 *
 * Muestra los detalles del equipo y un formulario para seleccionar un colaborador.
 * Las variables $equipo y $colaboradores son preparadas por InventarioController.
 */
?>

<a href="index.php?route=inventario">← Volver al Inventario</a>
<h1 class="mt-2"><?= $pageTitle ?? 'Asignar Equipo' ?></h1>

<div class="card mt-4">
    <div class="card-header">
        Asignar equipo: <strong><?= htmlspecialchars($equipo['nombre_equipo']) ?></strong> (ID: <?= $equipo['id'] ?>)
    </div>
    <div class="card-body">
        <p>Selecciona el colaborador al que se le asignará este equipo.</p>
        <form action="index.php?route=inventario&action=assign" method="POST">
            <input type="hidden" name="inventario_id" value="<?= $equipo['id'] ?>">

            <div class="mb-3">
                <label for="colaborador_id" class="form-label">Colaborador:</label>
                <select name="colaborador_id" id="colaborador_id" class="form-select" required>
                    <option value="">Seleccione un colaborador...</option>
                    <?php foreach ($colaboradores as $colaborador): ?>
                        <option value="<?= $colaborador['id'] ?>">
                            <?= htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Confirmar Asignación</button>
        </form>
    </div>
</div>