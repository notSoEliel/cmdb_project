<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $pageTitle ?? 'Iniciar Sesión' ?></title>

    <!-- Bootstrap CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <style>
        /* RESET GLOBAL */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* CONTENEDOR PRINCIPAL */
        .main {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .main {
                flex-direction: row;
            }
        }

        /* PANEL IZQUIERDO: degradado */
        .left {
            flex: 1;
            background: linear-gradient(-45deg, #0d6efd, #6f42c1, #20c997);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

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

        /* PANEL DERECHO: oculto en móvil, gris suave en desktop */
        .right {
            display: none;
        }

        @media (min-width: 768px) {
            .right {
                display: flex;
                flex: 1;
                justify-content: center;
                align-items: center;
                background: #f5f5f5;
                padding: 3rem;
            }
        }

        /* LOGIN WRAPPER COMÚN */
        .login-wrapper {
            width: 100%;
            max-width: 400px;
            border-radius: .75rem;
        }

        /* MÓVIL: formulario glassy sobre degradado */
        .login-wrapper.mobile {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 2rem;
            margin: 3rem auto 0;
        }

        .login-wrapper.mobile h2,
        .login-wrapper.mobile label.form-label {
            color: #fff;
        }

        .login-wrapper.mobile .form-control {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: .25rem;
            color: #000;
        }

        .login-wrapper.mobile .form-control::placeholder {
            color: #555;
        }

        /* Mostrar/ocultar form según tamaño */
        @media (min-width: 768px) {
            .login-wrapper.mobile {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .login-wrapper.desktop {
                display: none;
            }
        }

        /* Form desktop encima */
        .login-wrapper.desktop {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="main">

        <!-- PANEL IZQUIERDO -->
        <div class="left">
            <i class="bi bi-box-seam-fill" style="font-size:5rem;"></i>
            <h1 class="mt-3 display-5">CMDB System</h1>
            <p class="lead">Gestión de Activos y Configuración</p>

            <!-- FORMULARIO EN MÓVIL (glassy) -->
            <div class="login-wrapper mobile">
                <h2 class="mb-4 text-center">Bienvenido de Vuelta</h2>
                <form action="index.php?route=login&action=login" method="POST">
                    <div class="mb-3 form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="tú@correo.com" required>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="password-mobile" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password-mobile" name="password" placeholder="••••••••" required>
                            <button class="btn btn-outline-light" type="button" data-toggle="password">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid my-4">
                        <button type="submit" class="btn btn-light btn-lg">Ingresar</button>
                    </div>
                    <div class="text-center">
                        <a href="index.php?route=forgot-password&action=showForgotPasswordForm" class="text-white">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- PANEL DERECHO (desktop) -->
        <div class="right">
            <div class="login-wrapper desktop bg-white p-4">
                <h2 class="mb-4">Bienvenido de Vuelta</h2>
                <form action="index.php?route=login&action=login" method="POST">
                    <div class="mb-3 form-group">
                        <label for="email-desktop" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email-desktop" name="email" required>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="password-desktop" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password-desktop" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle="password">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid my-4">
                        <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                    </div>
                    <div class="text-center">
                        <a href="index.php?route=forgot-password&action=showForgotPasswordForm">¿Olvidaste tu contraseña?</a>
                        <span class="mx-2">|</span>
                        <a href="index.php?route=public&action=showMarketingPage">Portal de Noticias</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle “ver contraseña”
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    if (isset($_SESSION['mensaje_sa2'])) {
        $mensaje = json_encode($_SESSION['mensaje_sa2']);
        echo "<script>Swal.fire($mensaje);</script>";
        unset($_SESSION['mensaje_sa2']);
    }
    ?>

    <script>
        $(document).ready(function() {
            const validationConfig = {
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: 'El correo es obligatorio',
                        email: 'Introduce un correo válido'
                    },
                    password: {
                        required: 'La contraseña es obligatoria'
                    }
                },
                errorElement: 'div',
                errorClass: 'text-danger small mt-1'
            };
            // Se aplica la misma configuración a ambos formularios
            $('#form-login-mobile').validate(validationConfig);
            $('#form-login-desktop').validate(validationConfig);
        });
    </script>
</body>

</html>