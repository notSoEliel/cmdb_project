<!-- Al comienzo de tu vista, antes de cualquier estructura Bootstrap -->
<style>
    :root {
        --color-primary-dark: #2c3e50;
        --color-info-dark: #117a65;
        --color-success-dark:rgb(21, 92, 50);
        --color-warning-dark:rgb(29, 61, 119);
        --color-neutral-dark: #7f8c8d;
    }

    /* Bordes y fondos personalizados */
    .border-primary-dark {
        border-color: var(--color-primary-dark) !important;
    }

    .bg-primary-dark {
        background-color: var(--color-primary-dark) !important;
    }

    .border-info-dark {
        border-color: var(--color-info-dark) !important;
    }

    .bg-info-dark {
        background-color: var(--color-info-dark) !important;
    }

    .border-success-dark {
        border-color: var(--color-success-dark) !important;
    }

    .bg-success-dark {
        background-color: var(--color-success-dark) !important;
    }

    .border-warning-dark {
        border-color: var(--color-warning-dark) !important;
    }

    .bg-warning-dark {
        background-color: var(--color-warning-dark) !important;
    }

    /* Encabezados de tabla */
    .table-primary-dark thead {
        background-color: var(--color-primary-dark) !important;
        color: #fff;
    }

    .table-success-dark thead {
        background-color: var(--color-success-dark) !important;
        color: #fff;
    }

    .table-warning-dark thead {
        background-color: var(--color-warning-dark) !important;
        color: #000;
    }

    /* Botones de exportar */
    .btn-export-primary {
        background-color: var(--color-primary-dark);
        border-color: var(--color-primary-dark);
        color: #fff;
    }

    .btn-export-info {
        background-color: var(--color-info-dark);
        border-color: var(--color-info-dark);
        color: #fff;
    }

    .btn-export-success {
        background-color: var(--color-success-dark);
        border-color: var(--color-success-dark);
        color: #fff;
    }

    .btn-export-warning {
        background-color: var(--color-warning-dark);
        border-color: var(--color-warning-dark);
        color: #fff;
    }

    /* Hover ligero */
    .btn-export-primary:hover,
    .btn-export-info:hover,
    .btn-export-success:hover,
    .btn-export-warning:hover {
        filter: brightness(0.9);
    }
</style>

<h1 class="mb-4"><?= $pageTitle ?? 'Reportes' ?></h1>

<!-- Resumen de Inventario -->
<div class="card mb-4 border-primary-dark shadow-sm">
    <div class="card-header bg-primary-dark text-white border-primary-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Resumen de Inventario por Categoría</h5>
        <a href="index.php?route=reportes&action=exportarResumen" class="btn btn-sm btn-export-primary">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:300px; overflow:auto;">
            <table class="table table-sm table-hover mb-0 table-primary-dark">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Asignados</th>
                        <th class="text-center">Disponibles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totales = ['total' => 0, 'asignados' => 0, 'disponibles' => 0]; ?>
                    <?php if (empty($resumenPorCategoria)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-3">No hay datos para mostrar.</td>
                        </tr>
                        <?php else: foreach ($resumenPorCategoria as $r):
                            $totales['total']      += $r['total_equipos'];
                            $totales['asignados']  += $r['equipos_asignados'];
                            $totales['disponibles'] += $r['equipos_disponibles'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($r['categoria']) ?></td>
                                <td class="text-center"><?= $r['total_equipos'] ?></td>
                                <td class="text-center"><?= $r['equipos_asignados'] ?></td>
                                <td class="text-center"><?= $r['equipos_disponibles'] ?></td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
                <tfoot class="fw-semibold">
                    <tr class="table-primary-dark">
                        <td>TOTAL GENERAL</td>
                        <td class="text-center"><?= $totales['total'] ?></td>
                        <td class="text-center"><?= $totales['asignados'] ?></td>
                        <td class="text-center"><?= $totales['disponibles'] ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Gráfico -->
<div class="card mb-4 border-info-dark shadow-sm">
    <div class="card-header bg-info-dark text-white border-info-dark">
        <h5 class="mb-0">Visualización Gráfica</h5>
    </div>
    <div class="card-body p-0">
        <div class="position-relative" style="height:400px; width:100%;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Detalle de Inventario por Categoría -->
<div class="card mb-4 border-success-dark shadow-sm">
    <div class="card-header bg-success-dark text-white border-success-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reporte de Detalle de Inventario por Categoría</h5>
        <a href="index.php?route=reportes&action=exportarDetalladoPorCategoria" class="btn btn-sm btn-export-success">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($equiposPorCategoria)): ?>
            <ul class="nav nav-tabs mb-3" id="categoryTab" role="tablist">
                <?php $active = 'active';
                foreach (array_keys($equiposPorCategoria) as $cat): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $active ?>"
                            id="tab-<?= md5($cat) ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#content-<?= md5($cat) ?>"
                            type="button">
                            <?= htmlspecialchars($cat) ?>
                        </button>
                    </li>
                <?php $active = '';
                endforeach; ?>
            </ul>
            <div class="tab-content" id="categoryTabContent">
                <?php $show = 'show active';
                foreach ($equiposPorCategoria as $cat => $eqs): ?>
                    <div class="tab-pane fade <?= $show ?>" id="content-<?= md5($cat) ?>" role="tabpanel">
                        <div class="table-responsive" style="max-height:300px; overflow:auto;">
                            <table class="table table-sm table-hover mb-0 table-success-dark">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Serie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($eqs as $e): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($e['nombre_equipo']) ?></td>
                                            <td><?= htmlspecialchars($e['marca']) ?></td>
                                            <td><?= htmlspecialchars($e['modelo']) ?></td>
                                            <td><?= htmlspecialchars($e['serie']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php $show = '';
                endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay equipos para mostrar.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Asignaciones Activas -->
<div class="card border-warning-dark shadow-sm">
    <div class="card-header bg-warning-dark text-light border-warning-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reporte de Asignaciones Activas</h5>
        <a href="index.php?route=reportes&action=exportarAsignaciones" class="btn btn-sm btn-export-warning">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:300px; overflow:auto;">
            <table class="table table-sm table-hover mb-0 table-warning-dark">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Serie</th>
                        <th>Colaborador</th>
                        <th>Departamento</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asignacionesActivas as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['nombre_equipo']) ?></td>
                            <td><?= htmlspecialchars($a['serie']) ?></td>
                            <td><?= htmlspecialchars($a['nombre_colaborador']) ?></td>
                            <td><?= htmlspecialchars($a['departamento']) ?></td>
                            <td><?= (new DateTime($a['fecha_asignacion']))->format('d/m/Y') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $chartLabels ?? '[]' ?>,
                datasets: [{
                        label: 'Asignados',
                        data: <?= $chartDataAsignados ?? '[]' ?>,
                        backgroundColor: 'rgba(44,62,80,0.6)', // primary-dark ligero
                        borderColor: '#2c3e50',
                        borderWidth: 1
                    },
                    {
                        label: 'Disponibles',
                        data: <?= $chartDataDisponibles ?? '[]' ?>,
                        backgroundColor: 'rgba(30,132,73,0.6)', // success-dark ligero
                        borderColor: '#1e8449',
                        borderWidth: 1
                    },
                    {
                        label: 'Total de Equipos',
                        data: <?= $chartDataTotal ?? '[]' ?>,
                        backgroundColor: 'rgba(127,140,141,0.6)', // neutral-dark ligero
                        borderColor: '#7f8c8d',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 16
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            autoSkip: true
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                categoryPercentage: 0.8,
                barPercentage: 0.9
            }
        });
    });
</script>