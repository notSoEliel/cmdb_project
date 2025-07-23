<?php
// Obtenemos el rol del usuario y la ruta actual para la lógica del menú.
$userRole = $_SESSION['user_role'] ?? null;
$current_route = $_GET['route'] ?? 'home';
?>
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 h-100">
    <a href="<?= BASE_URL ?>" class="d-flex pt-5 pt-lg-3 align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-box-seam-fill me-2" style="font-size: 2rem;"></i>
        <span class="fs-4">CMDB System</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">

        <?php if ($userRole === 'admin'): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>" class="nav-link text-white <?= $current_route === 'home' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=inventario" class="nav-link text-white <?= ($current_route === 'inventario' && ($_GET['action'] ?? 'index') === 'index') ? 'active' : '' ?>">
                    <i class="bi bi-hdd-stack me-2"></i> Inventario
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=inventario&action=showDonados" class="nav-link text-white <?= ($_GET['action'] ?? '') === 'showDonados' ? 'active' : '' ?>">
                    <i class="bi bi-gift-fill me-2"></i> Donados
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=colaboradores" class="nav-link text-white <?= $current_route === 'colaboradores' ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i> Colaboradores
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=necesidades&action=adminIndex" class="nav-link text-white <?= $current_route === 'necesidades' ? 'active' : '' ?>">
                    <i class="bi bi-patch-question-fill me-2"></i>
                    Solicitudes
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=categorias" class="nav-link text-white <?= $current_route === 'categorias' ? 'active' : '' ?>">
                    <i class="bi bi-tags me-2"></i> Categorías
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=usuarios" class="nav-link text-white <?= $current_route === 'usuarios' ? 'active' : '' ?>">
                    <i class="bi bi-person-gear me-2"></i>
                    Usuarios Admin
                </a>
            </li>
        <?php endif; ?>

        <?php if ($userRole === 'colaborador'): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?route=portal" class="nav-link text-white <?= $current_route === 'portal' && ($_GET['action'] ?? 'index') === 'index' ? 'active' : '' ?>">
                    <i class="bi bi-person-workspace me-2"></i> Mi Portal
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=portal&action=misEquipos" class="nav-link text-white <?= ($_GET['action'] ?? '') === 'misEquipos' ? 'active' : '' ?>">
                    <i class="bi bi-hdd-stack-fill me-2"></i> Mis Equipos
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>index.php?route=necesidades&action=misSolicitudes" class="nav-link text-white <?= $current_route === 'necesidades' ? 'active' : '' ?>">
                    <i class="bi bi-patch-question me-2"></i>
                    Mis Solicitudes
                </a>
            </li>
        <?php endif; ?>

    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= BASE_URL . 'assets/default-avatar.png' ?>" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">

            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                <li>
                    <a class="dropdown-item" href="index.php?route=admin_profile">
                        <i class="bi bi-person-fill-gear me-2"></i>Mi Perfil (Admin)
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a class="dropdown-item" href="index.php?route=portal&action=showProfile">
                        <i class="bi bi-person-fill me-2"></i>Mi Perfil
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="index.php?route=logout&action=logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
        </ul>
    </div>
</div>