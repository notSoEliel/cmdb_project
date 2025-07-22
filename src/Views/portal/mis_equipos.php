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
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <img src="<?= BASE_URL . 'uploads/inventario/' . ($equipo['thumbnail_path'] ?? 'placeholder.png') ?>"
                        class="card-img-top"
                        alt="<?= htmlspecialchars($equipo['nombre_equipo']) ?>"
                        style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($equipo['nombre_equipo']) ?></h5>
                        <p class="card-text">
                            <strong>Marca:</strong> <?= htmlspecialchars($equipo['marca']) ?><br>
                            <strong>Modelo:</strong> <?= htmlspecialchars($equipo['modelo']) ?><br>
                            <strong>Serie:</strong> <?= htmlspecialchars($equipo['serie']) ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">Asignado el: <?= htmlspecialchars($equipo['fecha_asignacion'] ?? 'N/A') ?></small>
                        <a href="index.php?route=portal&action=showEquipoImages&id=<?= htmlspecialchars($equipo['id']) ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-images me-1"></i> Ver Imágenes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>