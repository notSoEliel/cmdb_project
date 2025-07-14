<?php
// Obtenemos la ruta actual para marcar el enlace activo
$current_route = $_GET['route'] ?? 'home';
?>
<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark h-100">
    <a href="<?= BASE_URL ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-box-seam-fill me-2" style="font-size: 2rem;"></i>
        <span class="fs-4">CMDB System</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= BASE_URL ?>" class="nav-link text-white <?= $current_route === 'home' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>index.php?route=inventario" class="nav-link text-white <?= $current_route === 'inventario' ? 'active' : '' ?>">
                <i class="bi bi-hdd-stack me-2"></i>
                Inventario
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>index.php?route=colaboradores" class="nav-link text-white <?= $current_route === 'colaboradores' ? 'active' : '' ?>">
                <i class="bi bi-people me-2"></i>
                Colaboradores
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>index.php?route=categorias" class="nav-link text-white <?= $current_route === 'categorias' ? 'active' : '' ?>">
                <i class="bi bi-tags me-2"></i>
                Categorías
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/32" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
            <li><a class="dropdown-item" href="#">Configuración</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Cerrar Sesión</a></li>
        </ul>
    </div>
</div>