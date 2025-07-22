<a href="index.php?route=necesidades&action=adminIndex">← Volver a todas las solicitudes</a>
<h1 class="mt-2 mb-4"><?= $pageTitle ?? 'Gestionar Solicitud' ?></h1>

<div class="card">
    <div class="card-header">
        Solicitud #<?= $solicitud['id'] ?> de <?= htmlspecialchars($solicitud['nombre_colaborador']) ?>
    </div>
    <div class="card-body">
        <p><strong>Descripción de la necesidad:</strong></p>
        <p class="ms-3"><em><?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?></em></p>
        <hr>
        <form action="index.php?route=necesidades&action=updateStatus" method="POST">
            <input type="hidden" name="necesidad_id" value="<?= $solicitud['id'] ?>">
            <div class="mb-3">
                <label for="estado" class="form-label"><strong>Cambiar estado:</strong></label>
                <select name="estado" id="estado" class="form-select">
                    <option value="Solicitado" <?= $solicitud['estado'] === 'Solicitado' ? 'selected' : '' ?>>Solicitado</option>
                    <option value="Aprobado" <?= $solicitud['estado'] === 'Aprobado' ? 'selected' : '' ?>>Aprobado</option>
                    <option value="Rechazado" <?= $solicitud['estado'] === 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
                    <option value="Completado" <?= $solicitud['estado'] === 'Completado' ? 'selected' : '' ?>>Completado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        </form>
    </div>
</div>