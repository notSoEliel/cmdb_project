<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no Encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold">404</h1>
        <p class="fs-3">Página no Encontrada</p>
        <p class="lead">
            La página o recurso que buscas no existe o ha sido movido.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <button onclick="window.history.back()" class="btn btn-secondary">← Regresar</button>
            <a href="<?= BASE_URL ?>" class="btn btn-primary">Ir al Inicio</a>
        </div>
        </div>
</body>
</html>