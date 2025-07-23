<h1 class="mb-4"><?= $pageTitle ?? 'Mis Equipos' ?></h1>

<div class="row">
    <?php if (empty($equipos)): ?>
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Actualmente no tienes ningún equipo asignado.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($equipos as $equipo): ?>
            <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <img src="<?= BASE_URL . 'uploads/inventario/' . ($equipo['thumbnail_path'] ?? 'placeholder.png') ?>"
                        class="card-img-top"
                        alt="<?= htmlspecialchars($equipo['nombre_equipo']) ?>"
                        style="height: 200px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate"><?= htmlspecialchars($equipo['nombre_equipo']) ?></h5>
                        <p class="card-text mb-2">
                            <strong>Marca:</strong> <?= htmlspecialchars($equipo['marca']) ?><br>
                            <strong>Modelo:</strong> <?= htmlspecialchars($equipo['modelo']) ?><br>
                            <strong>Serie:</strong> <?= htmlspecialchars($equipo['serie']) ?>
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>Estado:</strong>
                                    <?php
                                    $estado = htmlspecialchars($equipo['estado'] ?? 'Desconocido');
                                    $badgeClasses = [
                                        'Asignado' => 'bg-success',
                                        'En Reparación' => 'bg-warning text-dark',
                                        'Desactivado' => 'bg-secondary',
                                    ];
                                    $badgeClass = $badgeClasses[$estado] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $estado ?></span>
                                </div>

                                <?php if ($estado === 'Asignado'): ?>
                                    <form action="index.php?route=portal&action=reportarDano" method="POST" class="ms-2">
                                        <input type="hidden" name="inventario_id" value="<?= $equipo['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-exclamation-triangle"></i> Reportar Daño
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                        <small class="text-muted">Asignado el: <?= htmlspecialchars((new DateTime($equipo['fecha_asignacion'] ?? ''))->format('d/m/Y')) ?></small>
                        <a href="index.php?route=portal&action=showEquipoImages&id=<?= htmlspecialchars($equipo['id']) ?>"
                            class="btn btn-info btn-sm">
                            <i class="bi bi-images me-1"></i> Ver Imágenes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>