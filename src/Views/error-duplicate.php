<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conflicto de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-warning">409</h1>
        <p class="fs-3">Conflicto de Datos</p>
        <p class="lead">
            <?= htmlspecialchars($errorMessage ?? 'Los datos que intentas guardar ya existen.') ?>
        </p>
        <button onclick="window.history.back()" class="btn btn-primary">Volver y Corregir</button>
    </div>
</body>
</html>