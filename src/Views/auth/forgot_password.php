<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm" style="width: 100%; max-width: 450px;">
        <div class="card-body p-5">
            <h2 class="card-title text-center mb-4">Recuperar Contraseña</h2>
            <p class="text-muted text-center mb-4">Introduce tu correo y te enviaremos (o simularemos) un enlace para restablecer tu contraseña.</p>
            <form id="form-forgot-password" action="index.php?route=forgot-password&action=sendResetLink" method="POST">
                <div class="mb-3 form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="simulate_email" name="simulate_email" checked>
                    <label class="form-check-label" for="simulate_email">Simular envío de correo</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Enviar Enlace</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="index.php?route=login">Volver a Iniciar Sesión</a>
            </div>
        </div>
    </div>
</body>
</html>