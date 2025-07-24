<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $pageTitle ?? 'Mis Solicitudes' ?></h1>
    <a href="index.php?route=necesidades&action=showForm" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-2"></i>Crear Nueva Solicitud
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No has realizado ninguna solicitud.</td>
                        </tr>
                        <?php else: foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?= $solicitud['id'] ?></td>
                                <td><?= htmlspecialchars($solicitud['descripcion']) ?></td>
                                <td><?= (new DateTime($solicitud['fecha_solicitud']))->format('d/m/Y H:i') ?></td>
                                <td class="text-center">
                                    <?php
                                    $estado = htmlspecialchars($solicitud['estado']);
                                    $badges = [
                                        'Solicitado' => 'bg-secondary',
                                        'Aprobado'   => 'bg-info text-dark',
                                        'Rechazado'  => 'bg-danger',
                                        'Completado' => 'bg-success',
                                    ];
                                    $badge_class = $badges[$estado] ?? 'bg-light text-dark';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $estado ?></span>
                                </td>
                                <td class="text-end">
                                    <?php // Los botones solo aparecen si la solicitud está en un estado editable. 
                                    ?>
                                    <?php if (in_array($solicitud['estado'], ['Solicitado', 'Rechazado'])): ?>
                                        <a href="index.php?route=necesidades&action=showEditForm&id=<?= $solicitud['id'] ?>" class="btn btn-sm btn-warning" title="Editar Solicitud">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="index.php?route=necesidades&action=destroy" method="POST" class="d-inline form-delete">
                                            <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Solicitud">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>