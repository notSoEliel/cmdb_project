<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Portal del Colaborador</title>
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root {
      --bg: #f8f9fc;
      --fg: #343a40;
      --card-bg: #ffffff;
      --primary: #0d6efd;
      --success: #198754;
      --info:    #0dcaf0;
      --secondary: #6c757d;
    }

    body {
      background: var(--bg);
      color: var(--fg);
      font-family: 'Segoe UI', sans-serif;
      margin: 0; padding: 0;
    }

    h1 {
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 2rem;
      color: var(--primary);
    }

    .card {
      background: var(--card-bg);
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    /* ── Perfil ────────────────────────────────────── */
    .profile-card {
      padding: 2rem 1rem;
    }
    .profile-card img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid var(--primary);
      margin-bottom: 1rem;
    }
    .profile-card .card-title {
      color: var(--primary);
      font-size: 1.5rem;
      font-weight: 700;
    }
    .profile-card .card-text {
      color: #6c757d;
      margin-bottom: 1.5rem;
    }

    /* ── Estadísticas ─────────────────────────────── */
    .stats-card {
      padding: 1.5rem;
      color: #fff;
    }
    .stats-card .card-title {
      font-size: 0.85rem;
      text-transform: uppercase;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .stats-card .card-text {
      font-size: 2.25rem;
      font-weight: 700;
    }

    /* ── Links rápidos ────────────────────────────── */
    .links-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
    .link-card {
      padding: 1.75rem 1rem;
      text-align: center;
      display: flex;
      flex-direction: column;
    }
    .link-card i {
      font-size: 3rem;
      margin-bottom: 0.75rem;
    }
    .link-card .card-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .link-card .card-text {
      flex-grow: 1;
      color: #6c757d;
    }
    .link-card .btn {
      align-self: center;
    }

    @media (max-width: 576px) {
      h1 { font-size: 1.75rem; }
      .stats-card .card-text { font-size: 2rem; }
    }
  </style>
</head>
<body>
  <div class="container py-5">

    <h1>Portal del Colaborador</h1>

    <div class="row gx-4 gy-4 align-items-stretch">
      <!-- Perfil -->
      <div class="col-lg-5 col-md-6 mx-auto">
        <div class="card profile-card text-center shadow-sm">
          <div class="card-body">
            <?php
              $profilePhoto = !empty($colaborador['foto_perfil'])
                ? BASE_URL . 'uploads/colaboradores/' . htmlspecialchars($colaborador['foto_perfil'])
                : BASE_URL . 'public/assets/default-avatar.png';
            ?>
            <img src="<?= $profilePhoto ?>" alt="Foto de Perfil"/>
            <h5 class="card-title">¡Bienvenido, <?= htmlspecialchars($colaborador['nombre'] ?? 'Colaborador') ?>!</h5>
            <p class="card-text">Este es tu portal personal. Desde aquí gestiona tus recursos.</p>
            <a href="index.php?route=portal&action=showProfile"
               class="btn btn-outline-primary btn-sm">
              <i class="bi bi-person-fill-gear me-1"></i> Ver mi Perfil
            </a>
          </div>
        </div>
      </div>

      <!-- Mis estadísticas -->
      <div class="col-lg-7 col-md-12">
        <div class="row gx-3 gy-3 h-100">
          <div class="col-12">
            <div class="card stats-card bg-primary shadow-sm h-100">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Mis Equipos Asignados</h6>
                  <p class="card-text"><?= $totalEquiposAsignados ?></p>
                </div>
                <i class="bi bi-hdd-stack display-4"></i>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="card stats-card bg-info shadow-sm h-100">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-title">Solicitudes Pendientes</h6>
                  <p class="card-text"><?= $solicitudesColaboradorPendientes ?></p>
                </div>
                <i class="bi bi-patch-question-fill display-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Links rápidos -->
    <div class="links-grid">
      <div class="card link-card shadow-sm">
        <i class="bi bi-list-check text-success"></i>
        <h6 class="card-title">Mis Solicitudes</h6>
        <p class="card-text">Revisa el estado de tus solicitudes y crea nuevas.</p>
        <a href="index.php?route=necesidades&action=misSolicitudes"
           class="btn btn-sm btn-success mt-2">Ir a Solicitudes</a>
      </div>

      <div class="card link-card shadow-sm">
        <i class="bi bi-laptop text-primary"></i>
        <h6 class="card-title">Mis Equipos</h6>
        <p class="card-text">Visualiza los equipos que tienes actualmente asignados.</p>
        <a href="index.php?route=portal&action=misEquipos"
           class="btn btn-sm btn-primary mt-2">Ver mis Equipos</a>
      </div>

      <div class="card link-card shadow-sm">
        <i class="bi bi-info-circle text-secondary"></i>
        <h6 class="card-title">Acerca del Sistema</h6>
        <p class="card-text">Información sobre el CMDB y sus funcionalidades.</p>
        <a href="#"
           class="btn btn-sm btn-secondary mt-2">Saber Más</a>
      </div>
    </div>

  </div>
</body>
