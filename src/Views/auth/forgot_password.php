<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* degradado original */
    @keyframes gradient {
      0%   { background-position:   0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position:   0% 50%; }
    }
    body.gradient-bg {
      margin: 0;
      min-height: 100vh;
      background: linear-gradient(-45deg,rgb(157, 13, 253), #6f42c1,rgb(201, 32, 119));
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .glass-card {
      width: 100%;
      max-width: 450px;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: .75rem;
      padding: 2rem;
      box-shadow: 0 0 .5rem rgba(0,0,0,0.1);
    }
    label.error {
      color: #dc3545;
      font-size: .875em;
    }
  </style>
</head>
<body class="gradient-bg">
  <div class="glass-card">
    <h2 class="text-center mb-4">Recuperar Contraseña</h2>
    <p class="text-muted text-center mb-4">
      Introduce tu correo y simularemos el envío de un enlace para restablecer tu contraseña.
    </p>
    <form id="form-forgot-password"
          action="index.php?route=forgot-password&action=sendResetLink"
          method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          Enviar Enlace de Recuperación
        </button>
      </div>
    </form>
    <div class="text-center mt-3">
      <a href="index.php?route=login" class="text-white">Volver a Iniciar Sesión</a>
    </div>
  </div>

  <!-- scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?= $validationScript ?? '' ?>

  <?php
    if (isset($_SESSION['mensaje_sa2'])) {
        $mensaje = json_encode($_SESSION['mensaje_sa2']);
        echo "<script>Swal.fire($mensaje);</script>";
        unset($_SESSION['mensaje_sa2']);
    }
    ?>
</body>
</html>