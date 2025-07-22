<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $pageTitle ?? 'Mis Solicitudes' ?></h1>
    <a href="index.php?route=necesidades&action=showForm" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-2"></i>Crear Nueva Solicitud
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($solicitudes)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No has realizado ninguna solicitud.</td>
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
                                'Aprobado' => 'bg-info text-dark',
                                'Rechazado' => 'bg-danger',
                                'Completado' => 'bg-success',
                            ];
                            $badge_class = $badges[$estado] ?? 'bg-light text-dark';
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $estado ?></span>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>