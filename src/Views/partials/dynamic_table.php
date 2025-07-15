<?php

/**
 * Componente de Vista Reutilizable para Tablas Dinámicas
 *
 * Este archivo renderiza una tabla completa con controles de búsqueda,
 * ordenamiento, y paginación. Recibe toda su configuración a través
 * de la variable $tableConfig, que debe ser preparada en el controlador.
 */

// Desempaquetamos la configuración para un acceso más fácil
$columns = $tableConfig['columns'];
$data = $tableConfig['data'];
$pagination = $tableConfig['pagination'];
$actions = $tableConfig['actions'];

// Preparamos TODOS los parámetros de la URL para que no se pierdan al paginar u ordenar
$queryParams = [
    'route' => $_GET['route'] ?? '',
    'search' => $pagination['search'],
    'sort' => $pagination['sort'],
    'order' => $pagination['order'],
    'perPage' => $pagination['perPage'],
];
// Añadimos los filtros específicos (ej: categoria_id) si existen
if (!empty($pagination['filters'])) {
    $queryParams = array_merge($queryParams, $pagination['filters']);
}
?>

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex justify-content-between align-items-center">
            <input type="hidden" name="route" value="<?= htmlspecialchars($queryParams['route']) ?>">

            <div class="input-group" style="width: 50%;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="search" class="form-control" value="<?= htmlspecialchars($pagination['search']) ?>" placeholder="Buscar...">
            </div>

            <div class="d-flex align-items-center">
                <label for="perPage" class="form-label me-2 mb-0">Mostrar:</label>
                <select name="perPage" id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="5" <?= $pagination['perPage'] == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $pagination['perPage'] == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $pagination['perPage'] == 20 ? 'selected' : '' ?>>20</option>
                </select>
                <button class="btn btn-primary ms-2" type="submit">Aplicar</button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th>
                            <?php
                            $sortParams = $queryParams + ['sort' => $column['field'], 'order' => ($pagination['sort'] === $column['field'] && $pagination['order'] === 'asc') ? 'desc' : 'asc'];
                            ?>
                            <a href="?<?= http_build_query($sortParams) ?>">
                                <?= htmlspecialchars($column['header']) ?>
                                <?php if ($pagination['sort'] === $column['field']): ?>
                                    <i class="bi <?= $pagination['order'] === 'asc' ? 'bi-sort-up' : 'bi-sort-down' ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="<?= count($columns) + 1 ?>" class="text-center">No se encontraron registros que coincidan con su búsqueda.</td>
                    </tr>
                    <?php else: foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php if (isset($column['type']) && $column['type'] === 'image'): ?>
                                        <?php if (!empty($row[$column['field']])): ?>
                                            <img src="<?= BASE_URL . 'uploads/inventario/' . htmlspecialchars($row[$column['field']]) ?>" alt="Miniatura" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                        <?php else: ?>
                                            <span class="text-muted"><i class="bi bi-image" style="font-size: 1.5rem;"></i></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($row[$column['field']]) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-end">
                                <?php if (isset($actions['image_route'])): ?>
                                    <a href="?route=<?= $actions['image_route'] ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Gestionar Imágenes"><i class="bi bi-images"></i></a>
                                <?php endif; ?>
                                <a href="?route=<?= $actions['edit_route'] ?>&editar_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-fill"></i></a>

                                <form action="?route=<?= $actions['delete_route'] ?>" method="POST" class="d-inline form-delete">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span>Mostrando del <strong><?= $pagination['totalRecords'] > 0 ? (($pagination['currentPage'] - 1) * $pagination['perPage']) + 1 : 0 ?></strong> al <strong><?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['totalRecords']) ?></strong> de <strong><?= $pagination['totalRecords'] ?></strong> registros.</span>

        <nav>
            <ul class="pagination mb-0">
                <?php if ($pagination['totalPages'] > 1): ?>
                    <li class="page-item <?= ($pagination['currentPage'] <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query($queryParams + ['page' => $pagination['currentPage'] - 1]) ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <li class="page-item <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query($queryParams + ['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query($queryParams + ['page' => $pagination['currentPage'] + 1]) ?>">Siguiente</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>