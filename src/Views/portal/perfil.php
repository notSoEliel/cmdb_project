<?php

/**
 * Vista de Perfil del Colaborador
 * Muestra la información del usuario y permite editar campos específicos.
 */
?>

<h1 class="mb-4"><?= $pageTitle ?? 'Mi Perfil' ?></h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?= BASE_URL . 'uploads/colaboradores/' . htmlspecialchars($colaborador['foto_perfil'] ?? 'default.png') ?>"
                    alt="Foto de Perfil"
                    class="rounded-circle img-fluid"
                    style="width: 300px; height: 300px; object-fit: cover;">
                <h5 class="my-3"><?= htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']) ?></h5>
                <p class="text-body-secondary mb-1">Colaborador</p>
                <p class="text-body-secondary mb-4">ID: <?= htmlspecialchars($colaborador['identificacion_unica']) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0"><strong>Correo Electrónico</strong></p>
                    </div>
                    <div class="col-sm-9">
                        <p class="text-body-secondary mb-0"><?= htmlspecialchars($colaborador['email']) ?></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0"><strong>Teléfono</strong></p>
                    </div>
                    <div class="col-sm-9">
                        <p class="text-body-secondary mb-0"><?= htmlspecialchars($colaborador['telefono']) ?></p>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0"><strong>Ubicación Actual</strong></p>
                    </div>
                    <div class="col-sm-9">
                        <div id="view-location-div" class="d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0"><?= htmlspecialchars($colaborador['ubicacion']) ?></p>
                            <button class="btn btn-outline-primary btn-sm" id="btn-change-location">Cambiar</button>
                        </div>
                        <div id="edit-location-div" style="display: none;" class="py-4">
                            <form action="index.php?route=portal&action=updateLocation" method="POST">
                                <div class="input-group">
                                    <input require type="text" class="form-control" name="ubicacion" value="<?= htmlspecialchars($colaborador['ubicacion']) ?>">
                                    <button class="btn btn-primary" type="submit">Guardar</button>
                                    <button class="btn btn-secondary" type="button" id="btn-cancel-location">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <hr>

                <div id="password-section">
                    <div id="view-password-div" class="row">
                        <div class="col-sm-3">
                            <p class="mb-0"><strong>Contraseña</strong></p>
                        </div>
                        <div class="col-sm-9 d-flex justify-content-between align-items-center">
                            <p class="text-body-secondary mb-0">************</p>
                            <button class="btn btn-outline-primary btn-sm" id="btn-change-password">Cambiar Contraseña</button>
                        </div>
                    </div>

                    <div id="edit-password-div" style="display: none;">
                        <h5 class="mt-3">Cambiar Contraseña</h5>
                        <form id="form-password" action="index.php?route=portal&action=updatePassword" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3 form-group">
                                    <label for="current_password" class="form-label">Contraseña Actual</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword"><i class="bi bi-eye-fill"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword"><i class="bi bi-eye-fill"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 form-group">
                                    <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"><i class="bi bi-eye-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                            <button type="button" class="btn btn-secondary" id="btn-cancel-password">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>