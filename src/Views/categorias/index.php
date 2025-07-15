<?php
/**
 * Vista principal para la Gestión de Categorías.
 *
 * Muestra el formulario para agregar/editar y carga el componente de tabla dinámica.
 * La variable $tableConfig es preparada por el CategoriaController.
 */

// Lógica para obtener los datos de la categoría que se está editando
// para poder rellenar el formulario.
$categoriaActual = null;
if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
    $categoriaModel = new \App\Models\Categoria();
    $categoriaActual = $categoriaModel->findById((int)$_GET['editar_id']);
}
?>

<a href="index.php">← Volver al Menú Principal</a>
<h1 class="mt-2"><?= $pageTitle ?? 'Gestionar Categorías' ?></h1>

<div class="card mb-4">
    <div class="card-header"><?= $categoriaActual ? '✏️ Editando Categoría' : '➕ Agregar Nueva Categoría' ?></div>
    <div class="card-body">
        <form action="index.php?route=categorias&action=<?= $categoriaActual ? 'update' : 'store' ?>" method="POST">
            <input type="hidden" name="id" value="<?= $categoriaActual['id'] ?? '' ?>">
            <div class="input-group">
                <input type="text" class="form-control" name="nombre" placeholder="Nombre de la categoría" value="<?= htmlspecialchars($categoriaActual['nombre'] ?? '') ?>" required>
                <button class="btn btn-primary" type="submit">Guardar</button>
                <?php if ($categoriaActual): ?>
                    <a href="index.php?route=categorias" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php require_once '../src/Views/partials/dynamic_table.php'; ?>