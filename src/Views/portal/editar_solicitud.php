<a href="index.php?route=necesidades&action=misSolicitudes">← Volver a Mis Solicitudes</a>
<h1 class="mt-2 mb-4"><?= $pageTitle ?? 'Editar Solicitud' ?></h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?route=necesidades&action=save" method="POST">
            <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">
            <div class="mb-3">
                <label for="descripcion" class="form-label"><h5>Descripción de tu necesidad:</h5></label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="5" required><?= htmlspecialchars($solicitud['descripcion']) ?></textarea>
                <?php if ($solicitud['estado'] === 'Rechazado'): ?>
                    <div class="alert alert-warning mt-2">Nota: Al guardar, esta solicitud se volverá a abrir y la fecha se actualizará.</div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</div>