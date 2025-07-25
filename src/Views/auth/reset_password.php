<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
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
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: .75rem;
            padding: 2rem;
            box-shadow: 0 0 .5rem rgba(0, 0, 0, 0.1);
        }

        label.error {
            color: #dc3545;
            font-size: .875em;
        }
    </style>

    <style>
        button[data-toggle="password"] {
            background-color: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .input-group>button[data-toggle="password"] {
            border-left: none;
            border-top-right-radius: .25rem;
            border-bottom-right-radius: .25rem;
        }

        button[data-toggle="password"] i {
            color: #333 !important;
        }
    </style>
</head>

<body class="gradient-bg">
    <div class="glass-card">
        <h2 class="text-center mb-4">Nueva Contraseña</h2>
        <form id="form-reset-password"
            action="index.php?route=reset-password&action=resetPassword"
            method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            <div class="mb-3">
                <label for="new_password" class="form-label">Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="new_password" name="new_password">
                    <button class="btn btn-outline-secondary" type="button" data-toggle="password">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    <button class="btn btn-outline-secondary" type="button" data-toggle="password">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    Guardar Nueva Contraseña
                </button>
            </div>
        </form>
    </div>

    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="<?= BASE_URL ?>js/app.js"></script>
    <?= $validationScript ?? '' ?>
    <script>
        document.querySelectorAll('button[data-toggle="password"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.closest('.input-group').querySelector('input'),
                    icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
                }
            });
        });
    </script>
</body>

</html>