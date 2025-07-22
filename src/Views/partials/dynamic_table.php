<?php

/**
 * =======================================================================
 * COMPONENTE DE VISTA REUTILIZABLE PARA TABLAS DINÁMICAS
 * =======================================================================
 * Este archivo es el motor de todas nuestras tablas. Es un componente "tonto"
 * que recibe toda su configuración a través de la variable $tableConfig,
 * preparada por el controlador correspondiente.
 */

// --- 1. PREPARACIÓN DE VARIABLES ---
// Desempaquetamos la configuración para un acceso más fácil y limpio en el HTML.
$columns = $tableConfig['columns'];      // Define las columnas a mostrar.
$data = $tableConfig['data'];          // Los registros de la página actual.
$pagination = $tableConfig['pagination'];  // Contiene toda la info de paginación.
$actions = $tableConfig['actions'];      // Define las rutas para los botones de acción.
$currentRoute = $_GET['route'] ?? '';

// Preparamos TODOS los parámetros de la URL actual para que no se pierdan
// al hacer clic en los enlaces de paginación o de ordenamiento.
$queryParams = [
    'route' => $currentRoute,
    'search' => $pagination['search'],
    'sort' => $pagination['sort'],
    'order' => $pagination['order'],
    'perPage' => $pagination['perPage'],
];
// Añadimos filtros específicos (ej: categoria_id) si existen.
if (!empty($pagination['filters'])) {
    $queryParams = array_merge($queryParams, $pagination['filters']);
}
?>

<div class="card">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-center">
            <input type="hidden" name="route" value="<?= htmlspecialchars($currentRoute) ?>">

            <div class="col-md-4">
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
                            <select name="<?= htmlspecialchars($filter['name']) ?>" class="form-select" onchange="this.form.submit()">
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

            <div class="col d-flex justify-content-end align-items-center">
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
                                <?php if (isset($column['sort_by'])) : // Si la columna es ordenable... 
                                ?>
                                    <?php $sortParams = array_merge($queryParams, ['sort' => $column['sort_by'], 'order' => ($pagination['sort'] === $column['sort_by'] && $pagination['order'] === 'asc') ? 'desc' : 'asc']); ?>
                                    <a href="?<?= http_build_query($sortParams) ?>"><?= htmlspecialchars($column['header']) ?><?php if ($pagination['sort'] === $column['sort_by']) : ?> <i class="bi <?= $pagination['order'] === 'asc' ? 'bi-sort-up' : 'bi-sort-down' ?>"></i><?php endif; ?></a>
                                <?php else: // Si no, solo muestra el texto. 
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
                                <?php foreach ($columns as $index => $column) : ?>
                                    <td class="<?= ($index == 0) ? 'sticky-col first-col' : '' ?>">
                                        <?php
                                        // Renderizado condicional basado en el tipo de columna
                                        $cellValue = $row[$column['field']] ?? 'N/A';
                                        if (isset($column['type']) && $column['type'] === 'image') {
                                            if (!empty($cellValue)) {
                                                echo '<img src="' . BASE_URL . 'uploads/inventario/' . htmlspecialchars($cellValue) . '" alt="Miniatura" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">';
                                            } else {
                                                echo '<span class="text-muted"><i class="bi bi-image" style="font-size: 1.5rem;"></i></span>';
                                            }
                                        } elseif ($column['field'] === 'estado') {
                                            // Lógica para mostrar insignias de colores para el estado
                                            $badges = ['En Stock' => 'bg-success', 'Asignado' => 'bg-primary', 'En Reparación' => 'bg-info text-dark', 'Dañado' => 'bg-warning text-dark', 'En Descarte' => 'bg-secondary', 'Donado' => 'bg-dark'];
                                            $badge_class = $badges[$cellValue] ?? 'bg-light text-dark';
                                            echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars($cellValue) . '</span>';
                                        } elseif ($column['field'] === 'activo') {
                                            // Nueva lógica para los badges de estado de USUARIO/ADMIN
                                            if ($cellValue == 1) {
                                                echo '<span class="badge bg-success">Activo</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">Inactivo</span>';
                                            }
                                        } elseif ($column['field'] === 'fecha_fin_vida' && !empty($row['fecha_fin_vida'])) {
                                            $fechaFin = new DateTime($row['fecha_fin_vida']);
                                            $hoy = new DateTime();
                                            $diferencia = $hoy->diff($fechaFin);

                                            // Muestra la fecha
                                            echo $fechaFin->format('Y-m-d');

                                            // Muestra una insignia si ya expiró o le quedan menos de 6 meses
                                            if ($hoy > $fechaFin) {
                                                echo ' <span class="badge bg-danger">Expirado</span>';
                                            } elseif ($diferencia->y == 0 && $diferencia->m < 6) {
                                                echo ' <span class="badge bg-warning text-dark">Próximo a Expirar</span>';
                                            }
                                        } else {
                                            // Si es una celda normal, solo muestra el texto
                                            echo htmlspecialchars($cellValue);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>

                                <td class="text-end text-nowrap sticky-col last-col">
                                    <?php if ($currentRoute === 'inventario') : ?>
                                        <?php if (!empty($row['notas_donacion'])): /* Botón de Notas */ ?>
                                            <button type="button" class="btn btn-sm btn-dark" title="Ver Notas" data-bs-toggle="modal" data-bs-target="#notesModal" data-notes="<?= htmlspecialchars($row['notas_donacion']) ?>"><i class="bi bi-card-text"></i></button>
                                        <?php endif; ?>
                                        <?php if ($row['estado'] === 'En Stock') : /* Botón para Asignar */ ?>
                                            <a href="?route=inventario&action=showAssignForm&id=<?= $row['id'] ?>" class="btn btn-sm btn-success" title="Asignar Equipo"><i class="bi bi-person-plus-fill"></i></a>
                                        <?php elseif (!empty($row['asignacion_id'])) : /* Botón para Des-asignar */ ?>
                                            <form action="?route=inventario&action=unassign" method="POST" class="d-inline form-delete"><input type="hidden" name="inventario_id" value="<?= $row['id'] ?>"><input type="hidden" name="asignacion_id" value="<?= $row['asignacion_id'] ?>"><button type="submit" class="btn btn-sm btn-outline-danger" title="Des-asignar"><i class="bi bi-person-dash-fill"></i></button></form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($currentRoute === 'colaboradores') : ?>
                                        <a href="?route=inventario&colaborador_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Ver Equipos Asignados"><i class="bi bi-hdd-stack"></i></a>
                                    <?php endif; ?>

                                    <?php if (isset($actions['image_action'])) : ?>
                                        <a href="?route=<?= $actions['route'] ?>&action=<?= $actions['image_action'] ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Gestionar Imágenes"><i class="bi bi-images"></i></a>
                                    <?php endif; ?>

                                    <?php if (isset($actions['edit_action'])) : ?>
                                        <?php // CASO ESPECIAL: Para la tabla de 'necesidades', creamos un enlace simple a la página de edición. 
                                        ?>
                                        <?php if ($currentRoute === 'necesidades'): ?>
                                            <a href="?route=necesidades&action=<?= $actions['edit_action'] ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Gestionar Solicitud"><i class="bi bi-pencil-fill"></i></a>

                                        <?php else: // CASO GENERAL: Para todas las demás tablas, creamos un enlace que preserva los filtros.
                                        ?>
                                            <?php
                                            $editParams = array_merge($queryParams, [
                                                'action' => $actions['edit_action'],
                                                'editar_id' => $row['id']
                                            ]);
                                            ?>
                                            <a href="?<?= http_build_query($editParams) ?>" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (isset($actions['delete_action'])) : ?>
                                        <form action="?route=<?= $actions['route'] ?>&action=<?= $actions['delete_action'] ?>" method="POST" class="d-inline form-delete"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash-fill"></i></button></form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <span>Mostrando del <strong><?= $pagination['totalRecords'] > 0 ? (($pagination['currentPage'] - 1) * $pagination['perPage']) + 1 : 0 ?></strong> al <strong><?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['totalRecords']) ?></strong> de <strong><?= $pagination['totalRecords'] ?></strong> registros.</span>

        <nav>
            <ul class="pagination mb-0">
                <?php if ($pagination['totalPages'] > 1): ?>

                    <li class="page-item <?= ($pagination['currentPage'] <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => 1])) ?>">« Primera</a>
                    </li>

                    <li class="page-item <?= ($pagination['currentPage'] <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $pagination['currentPage'] - 1])) ?>">Anterior</a>
                    </li>

                    <?php
                    // Lógica para mostrar un rango de páginas (ej: 3 botones)
                    $range = 1;
                    $start = max(1, $pagination['currentPage'] - $range);
                    $end = min($pagination['totalPages'], $pagination['currentPage'] + $range);

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <li class="page-item <?= ($i == $pagination['currentPage']) ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $pagination['currentPage'] + 1])) ?>">Siguiente</a>
                    </li>

                    <li class="page-item <?= ($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $pagination['totalPages']])) ?>">Última »</a>
                    </li>

                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>