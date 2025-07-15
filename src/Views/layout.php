<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'CMDB' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }

        .sidebar {
            width: 280px;
            flex-shrink: 0;
        }

        .content-wrapper {
            flex-grow: 1;
            padding: 2rem;
            background-color: #f8f9fa;
            min-width: 0;
        }

        label.error {
            color: #dc3545;
            font-size: 0.875em;
        }

        input.error,
        select.error {
            border-color: #dc3545 !important;
        }

        .table-responsive-sticky {
            overflow-x: auto;
            position: relative;
        }

        .table-responsive-sticky th,
        .table-responsive-sticky td {
            white-space: nowrap;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: #ffffff;
            /* Un fondo blanco se ve mejor en las celdas fijas */
        }

        .first-col {
            left: 0;
            z-index: 2;
        }

        .last-col {
            right: 0;
            z-index: 2;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <?php require_once 'partials/sidebar.php'; // Incluimos el menú lateral 
        ?>
    </div>

    <main class="content-wrapper">
        <?= $content ?? '' // Aquí se renderizará el contenido de cada página 
        ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        <?php
        if (isset($_SESSION['mensaje_sa2'])) {
            $mensaje = json_encode($_SESSION['mensaje_sa2']);
            echo "Swal.fire($mensaje);";
            unset($_SESSION['mensaje_sa2']);
        }
        ?>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

    <script src="<?= BASE_URL ?>js/app.js"></script> </body>
</body>

</html>