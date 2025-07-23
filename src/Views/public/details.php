<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Detalles del Activo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-3"> <div class="container"> <div class="card shadow-sm">
            <div class="card-header fs-5 text-white bg-dark py-3"> Detalles del Activo
            </div>
            <div class="card-body p-3"> <h5 class="card-title mb-2"><?= htmlspecialchars($equipo['nombre_equipo']) ?></h5> <ul class="list-group list-group-flush">
                    <li class="list-group-item py-2"><strong>ID:</strong> <span class="fw-light"><?= $equipo['id'] ?></span></li> <li class="list-group-item py-2"><strong>Categoría:</strong> <span class="fw-light"><?= htmlspecialchars($equipo['nombre_categoria']) ?></span></li>
                    <li class="list-group-item py-2"><strong>Marca:</strong> <span class="fw-light"><?= htmlspecialchars($equipo['marca']) ?></span></li>
                    <li class="list-group-item py-2"><strong>Modelo:</strong> <span class="fw-light"><?= htmlspecialchars($equipo['modelo']) ?></span></li>
                    <li class="list-group-item py-2"><strong>Serie:</strong> <span class="fw-light"><?= htmlspecialchars($equipo['serie']) ?></span></li>
                    <li class="list-group-item py-2">
                        <strong>Costo Adquisición:</strong>
                        <span class="fw-light">$<?= number_format($equipo['costo'], 2) ?></span>
                    </li>
                    <li class="list-group-item py-2">
                        <strong>Fecha de Adquisición:</strong>
                        <span class="fw-light"><?= (new DateTime($equipo['fecha_ingreso']))->format('d/m/Y') ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <p class="mt-3 text-center text-muted small"> Escanea este código QR para acceder a esta información.
        </p>
    </div>
</body>
</html>