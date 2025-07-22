<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'CMDB' ?></title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tu CSS principal -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>

<body>
    <!-- Botón Hamburguesa (z-index alto para que siempre esté encima del overlay) -->
    <button
        class="btn toggle-sidebar d-lg-none"
        style="position: fixed; top: 1rem; left: 1rem; z-index: 115;">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
   <?php require_once 'partials/sidebar.php'; ?>

    <!-- Overlay (ahora justo después del sidebar, para poder usar + si quisieras) -->
    <div class="sidebar-overlay d-lg-none"></div>

    <!-- Contenido principal -->
    <main class="content-wrapper">
        <?= $content ?? '' ?>
    </main>

    <!-- Scripts -->
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
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            toggleBtn?.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            });

            overlay?.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.style.display = 'none';
            });

            // Al pasar a desktop, cerramos el sidebar y ocultamos overlay
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            });
        });
    </script>

    <?= $validationScript ?? '' ?>
    <script src="<?= BASE_URL ?>js/app.js"></script>
</body>

</html>