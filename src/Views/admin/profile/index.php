<h1 class="mb-4"><?= $pageTitle ?? 'Mi Perfil' ?></h1>
<div class="card">
    <div class="card-header">
        Información de la Cuenta
    </div>
    <div class="card-body">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($admin['nombre']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
        <hr>
        <h5 class="mt-4">Cambiar Contraseña</h5>
        <form id="form-admin-password" action="index.php?route=admin_profile&action=updatePassword" method="POST">
            <div class="row">
                <div class="col-md-12 mb-3 form-group">
                    <label for="current_password" class="form-label">Contraseña Actual</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword"><i class="bi bi-eye-fill"></i></button>
                    </div>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword"><i class="bi bi-eye-fill"></i></button>
                    </div>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"><i class="bi bi-eye-fill"></i></button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
        </form>
    </div>
</div>