<a href="index.php?route=inventario">← Volver al Inventario Principal</a>
<h1 class="mt-2 mb-4"><?= $pageTitle ?? 'Equipos Descartados' ?></h1>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>
<?php require_once '../src/Views/partials/notes_modal.php'; ?>
