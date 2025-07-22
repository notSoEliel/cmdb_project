<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Login' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h1 class="card-title text-center mb-4">Iniciar Sesión</h1>
                <form action="index.php?route=login&action=login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    if (isset($_SESSION['mensaje_sa2'])) {
        $mensaje = json_encode($_SESSION['mensaje_sa2']);
        echo "<script>Swal.fire($mensaje);</script>";
        unset($_SESSION['mensaje_sa2']);
    }
    ?>
</body>

</html>