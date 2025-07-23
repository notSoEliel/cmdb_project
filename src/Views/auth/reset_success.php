<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contraseña Restablecida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="5;url=<?= BASE_URL ?>index.php?route=login">
    <style>
        /* degradado nuevo, suave y rápido */
        @keyframes gradientBG {
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
            background: linear-gradient(-45deg, #2193b0, #6dd5ed, #cc2b5e, #ee9ca7);
            background-size: 400% 400%;
            animation: gradientBG 5s ease infinite;
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
            text-align: center;
        }
    </style>
</head>

<body class="gradient-bg">
    <div class="glass-card">
        <h2 class="text-success mb-4">¡Éxito!</h2>
        <p class="lead">Tu contraseña ha sido restablecida correctamente.</p>
        <p class="text-muted">
            Serás redirigido en <strong><span id="countdown">5</span> segundos</strong>...
        </p>
        <a href="index.php?route=login" class="btn btn-primary mt-3">
            Iniciar Sesión Ahora
        </a>
    </div>

    <script>
        (function() {
            let s = 5;
            const el = document.getElementById('countdown'),
                url = '<?= BASE_URL ?>index.php?route=login';
            const iv = setInterval(() => {
                if (--s <= 0) {
                    clearInterval(iv);
                    window.location.href = url;
                } else {
                    el.textContent = s;
                }
            }, 1000);
        })();
    </script>
</body>

</html>