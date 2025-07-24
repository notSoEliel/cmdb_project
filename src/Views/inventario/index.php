<?php

/**
 * Vista principal para la Gestión de Inventario.
 *
 * Este archivo ahora solo se encarga de mostrar la tabla de inventario
 * y los botones de acción para añadir o exportar.
 * Los formularios de añadir/editar se han movido a 'Views/inventario/add_edit.php'.
 *
 * Variables esperadas:
 * - $tableConfig: Un array con toda la configuración para la tabla dinámica.
 * - $pageTitle: El título de la página.
 */
?>

<div class="d-flex justify-content-between align-items-center">
    <h1 class="mt-2"><?= $pageTitle ?? 'Gestionar Inventario' ?></h1>
    <div>
        <a href="index.php?route=inventario&action=showDonados" class="btn btn-outline-secondary me-2">
            <i class="bi bi-gift me-2"></i>Ver Donados
        </a>
        <a href="index.php?route=inventario&action=showAddForm" class="btn btn-primary me-2">
            <i class="bi bi-plus-circle-fill me-2"></i>Añadir Nuevo Equipo
        </a>
    </div>
</div>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>
<?php require_once '../src/Views/partials/notes_modal.php'; // Incluir el modal de notas reutilizable ?>

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