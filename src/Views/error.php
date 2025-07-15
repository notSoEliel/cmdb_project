<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold">Oops!</h1>
        <p class="fs-3"> <span class="text-danger">Algo salió mal.</span></p>
        <p class="lead">
            <?= htmlspecialchars($errorMessage ?? 'Ocurrió un error inesperado.') ?>
        </p>
        <button onclick="window.history.back()" class="btn btn-warning">Volver e Intentarlo</button>
        <a href="<?= BASE_URL ?>" class="btn btn-primary">Ir a la Página Principal</a>
    </div>
</body>
</html>