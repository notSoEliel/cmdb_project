<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'CMDB' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/pristinejs/dist/pristine.css"> </head>
        <style>
        /* Estilos para los mensajes de error de jQuery Validate */
        label.error {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        input.error, select.error {
            border-color: #dc3545;
        }
    </style>
</head>

<body>
    <main class="container">
        <?= $content ?? '' ?>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        if (isset($_SESSION['mensaje_sa2'])) {
            // Usamos json_encode para pasar el array PHP a un objeto JavaScript de forma segura
            $mensaje = json_encode($_SESSION['mensaje_sa2']);
            echo "Swal.fire($mensaje);";
            unset($_SESSION['mensaje_sa2']); // Limpiamos para que no vuelva a salir
        } else  {
            echo 'no está jaja';
        }
        ?>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica para las alertas de confirmación de borrado
            document.querySelectorAll('.form-delete').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, ¡bórralo!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
    <?= $validationScript ?? '' ?>
</body>

</html>