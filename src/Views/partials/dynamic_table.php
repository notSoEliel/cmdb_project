<?php

/**
 * =======================================================================
 * COMPONENTE DE VISTA REUTILIZABLE PARA TABLAS DINÁMICAS
 * =======================================================================
 * Este archivo renderiza una tabla completa con controles de búsqueda,
 * filtros, ordenamiento y paginación. Recibe toda su configuración
 * a través de la variable $tableConfig, preparada en el controlador.
 */

// --- 1. PREPARACIÓN DE VARIABLES ---
$columns = $tableConfig['columns'];
$data = $tableConfig['data'];
$pagination = $tableConfig['pagination'];
$actions = $tableConfig['actions'];
$currentRoute = $_GET['route'] ?? '';

// Preparamos los parámetros de la URL para que no se pierdan al paginar.
$queryParams = [
    'route' => $currentRoute,
    'search' => $pagination['search'],
    'sort' => $pagination['sort'],
    'order' => $pagination['order'],
    'perPage' => $pagination['perPage'],
];
if (!empty($pagination['filters'])) {
    $queryParams = array_merge($queryParams, $pagination['filters']);
}
?>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="route" value="<?= htmlspecialchars($currentRoute) ?>">

            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control" value="<?= htmlspecialchars($pagination['search']) ?>" placeholder="Buscar...">
                </div>
            </div>

            <?php if (isset($tableConfig['dropdown_filters'])) : ?>
                <?php foreach ($tableConfig['dropdown_filters'] as $filter) : ?>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><?= htmlspecialchars($filter['label']) ?>:</span>
                            <select name="<?= htmlspecialchars($filter['name']) ?>" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($filter['options'] as $option) : ?>
                                    <option value="<?= $option['id'] ?>" <?= (isset($_GET[$filter['name']]) && $_GET[$filter['name']] == $option['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($option['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="col-md-3 d-flex justify-content-end align-items-center">
                <label for="perPage" class="form-label me-2 mb-0 text-nowrap">Por página:</label>
                <select name="perPage" id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="5" <?= $pagination['perPage'] == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $pagination['perPage'] == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $pagination['perPage'] == 20 ? 'selected' : '' ?>>20</option>
                </select>
                <button class="btn btn-primary btn-sm ms-2" type="submit">Aplicar</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive-sticky">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <?php foreach ($columns as $index => $column) : ?>
                            <th class="<?= ($index == 0) ? 'sticky-col first-col' : '' ?>">
                                <?php if (isset($column['sort_by'])) : // Si la columna es ordenable 
                                ?>
                                    <?php
                                    $sortField = $column['sort_by'];

                                    // --- INICIO DE LA CORRECCIÓN CLAVE ---
                                    // Usamos array_merge() en lugar del operador +
                                    // array_merge() SÍ sobreescribe los valores 'sort' y 'order' existentes.
                                    $sortParams = array_merge($queryParams, [
                                        'sort' => $sortField,
                                        'order' => ($pagination['sort'] === $sortField && $pagination['order'] === 'asc') ? 'desc' : 'asc'
                                    ]);
                                    // --- FIN DE LA CORRECCIÓN CLAVE ---
                                    ?>

                                    <a href="?<?= http_build_query($sortParams) ?>">
                                        <?= htmlspecialchars($column['header']) ?>
                                        <?php if ($pagination['sort'] === $sortField) : ?>
                                            <i class="bi <?= $pagination['order'] === 'asc' ? 'bi-sort-up' : 'bi-sort-down' ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                <?php else: // Si no es ordenable, solo muestra el texto 
                                ?>
                                    <?= htmlspecialchars($column['header']) ?>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="text-end sticky-col last-col">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($data)) : ?>
                        <tr>
                            <td colspan="<?= count($columns) + 1 ?>" class="text-center">No se encontraron registros.</td>
                        </tr>
                        <?php else : foreach ($data as $row) : ?>
                            <tr>
                                <?php // --- INICIO DE LA MODIFICACIÓN EN CELDAS DE DATOS --- 
                                ?>
                                <?php foreach ($columns as $index => $column) : ?>
                                    <td class="<?= ($index == 0) ? 'sticky-col first-col' : '' ?>">
                                        <?php if (isset($column['type']) && $column['type'] === 'image') : ?>
                                            <?php if (!empty($row[$column['field']])) : ?>
                                                <img src="<?= BASE_URL . 'uploads/inventario/' . htmlspecialchars($row[$column['field']]) ?>" alt="Miniatura" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                                            <?php else : ?>
                                                <span class="text-muted"><i class="bi bi-image" style="font-size: 1.5rem;"></i></span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <?= htmlspecialchars($row[$column['field']] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>

                                <td class="text-end text-nowrap sticky-col last-col">
                                    <?php if ($currentRoute === 'inventario') : ?>
                                        <?php if ($row['estado'] === 'En Stock') : ?>
                                            <a href="?route=inventario&action=showAssignForm&id=<?= $row['id'] ?>" class="btn btn-sm btn-success" title="Asignar Equipo"><i class="bi bi-person-plus-fill"></i></a>
                                        <?php elseif ($row['estado'] === 'Asignado') : ?>
                                            <form action="?route=<?= $actions['unassign_route'] ?>" method="POST" class="d-inline form-unassign">
                                                <input type="hidden" name="inventario_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="asignacion_id" value="<?= $row['asignacion_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Des-asignar"><i class="bi bi-person-dash-fill"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($currentRoute === 'colaboradores') : ?>
                                        <a href="?route=inventario&colaborador_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Ver Equipos Asignados"><i class="bi bi-hdd-stack"></i></a>
                                    <?php endif; ?>

                                    <?php if (isset($actions['image_route'])) : ?>
                                        <a href="?route=<?= $actions['image_route'] ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Gestionar Imágenes"><i class="bi bi-images"></i></a>
                                    <?php endif; ?>

                                    <?php
                                    $editParams = array_merge($queryParams, ['editar_id' => $row['id']]);
                                    ?>
                                    <a href="?<?= http_build_query($editParams) ?>" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-fill"></i></a>

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
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
    </div>
</div>