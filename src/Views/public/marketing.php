<?php
// --- GESTIÓN DE CONTENIDO ESTÁTICO ---
$posts = [
    ['title' => '¿Por Qué Cada Empresa Necesita un Sistema de Inventario?', 'category' => 'Gestión de Inventario', 'author' => 'Ana C.', 'date' => '2025-07-22', 'image' => 'https://images.unsplash.com/photo-1556740714-a8395b3bf30f?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => 'Descubre cómo un control preciso de tus activos de hardware y software puede reducir costos, mejorar la seguridad y optimizar la toma de decisiones en tu organización.'],
    ['title' => 'Maximizando la Productividad con Software Colaborativo', 'category' => 'Software', 'author' => 'Carlos R.', 'date' => '2025-07-20', 'image' => 'https://images.unsplash.com/photo-1712903276265-952cee2dd1be?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => 'Las herramientas adecuadas pueden transformar la forma en que tu equipo trabaja. Analizamos las licencias más importantes para el entorno de oficina moderno.'],
    ['title' => 'Tendencias en Hardware para Oficinas en 2025', 'category' => 'Hardware', 'author' => 'Laura M.', 'date' => '2025-07-18', 'image' => 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => 'Desde procesadores de bajo consumo hasta monitores ergonómicos 4K, exploramos el hardware que definirá los espacios de trabajo del futuro.'],
    ['title' => 'El Ciclo de Vida de un Activo de TI: Más Allá de la Compra', 'category' => 'Gestión de Inventario', 'author' => 'Ana C.', 'date' => '2025-07-15', 'image' => 'https://images.unsplash.com/photo-1581090466619-e945d2e2980e?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => 'Un verdadero CMDB no solo registra lo que tienes, sino que gestiona todo su ciclo: asignación, reparación, descarte y donación.'],
    ['title' => 'Seguridad en Licencias de Software: Un Pilar Olvidado', 'category' => 'Software', 'author' => 'Carlos R.', 'date' => '2025-07-12', 'image' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => 'Evita multas y riesgos de seguridad. Aprende a gestionar tus licencias de forma eficiente para garantizar el cumplimiento y optimizar la inversión.'],
    ['title' => 'El Futuro del Almacenamiento: SSDs vs. Cloud', 'category' => 'Hardware', 'author' => 'Laura M.', 'date' => '2025-07-10', 'image' => 'https://plus.unsplash.com/premium_photo-1688678097492-bae9a5000d40?q=80&w=2048&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'excerpt' => '¿Qué es mejor para tu empresa? Analizamos los pros y los contras del almacenamiento local de alta velocidad frente a la flexibilidad de la nube.'],
];
$featuredPost = array_shift($posts);

// Calcular días desde publicación para el timeline
$today = new DateTime();
$diffs = [];
foreach ($posts as $p) {
    $diffs[] = $today->diff(new DateTime($p['date']))->days;
}
$maxDiff = max($diffs) ?: 1;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMDB System – WOW UX/UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
        }

        body {
            background: #f8f9fa;
            color: #212529;
            transition: .3s;
        }

        .card {
            background: #fff;
            transition: transform .3s, box-shadow .3s;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .2);
        }

        /* Parallax hero */
        .featured-post {
            background: url('<?= $featuredPost['image'] ?>') center/cover fixed no-repeat;
            height: 450px;
            display: flex;
            align-items: flex-end;
            color: #fff;
            position: relative;
        }

        .featured-overlay {
            background: rgba(0, 0, 0, .5);
            padding: 2rem;
            width: 100%;
        }

        /* Fade-in */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: .6s;
        }

        .fade-in.visible {
            opacity: 1;
            transform: none;
        }

        /* Ticker */
        #news-ticker {
            background: var(--primary);
            color: #fff;
            overflow: hidden;
            white-space: nowrap;
        }

        #news-ticker span {
            display: inline-block;
            padding-left: 100%;
            animation: ticker 15s linear infinite;
        }

        @keyframes ticker {
            from {
                transform: translateX(0)
            }

            to {
                transform: translateX(-100%)
            }
        }

        /* Timeline bar */
        .timeline-bar {
            background: #e9ecef;
            border-radius: 4px;
            height: 8px;
            overflow: hidden;
        }

        .timeline-fill {
            background: var(--primary);
            height: 100%;
            transition: width .4s;
        }

        /* Search */
        #searchInput {
            max-width: 400px;
        }

        /* Scroll progress */
        #scrollProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: var(--primary);
            width: 0;
            z-index: 1080;
        }

        /* Scroll-to-top */
        #scrollTopBtn {
            position: fixed;
            bottom: 40px;
            right: 20px;
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1080;
        }

        #scrollTopBtn i {
            font-size: 1.2rem;
        }

        /* Confetti */
        @keyframes confetti-fall {
            0% {
                opacity: 1;
                transform: translateY(0) rotateZ(0);
            }

            100% {
                opacity: 0;
                transform: translateY(100vh) rotateZ(360deg);
            }
        }

        .confetti-piece {
            position: fixed;
            width: 6px;
            height: 10px;
            opacity: 1;
            pointer-events: none;
            z-index: 1100;
            animation: confetti-fall 2s ease-out forwards;
        }

        /* Toast container */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1090;
        }

        /* Notifications modal override */
        #notificationsModal .modal-dialog {
            position: absolute;
            top: 60px;
            right: 20px;
            margin: 0;
            max-width: 300px;
        }
    </style>
</head>

<body>
    <!-- Scroll Progress Bar -->
    <div id="scrollProgress"></div>

    <!-- Navbar con Centro de Notificaciones -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-box-seam-fill me-2"></i><strong>CMDB System</strong></a>
            <div class="ms-auto d-flex align-items-center">
                <button id="notificationsBtn" class="btn btn-outline-light btn-sm me-3 position-relative" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                    <i class="bi bi-bell"></i>
                    <span id="notificationCount" class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">0</span>
                </button>
                <a href="index.php?route=login" class="btn btn-primary">Iniciar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Ticker de Noticias -->
    <div id="news-ticker"><span id="ticker-text">Bienvenidos a CMDB System – ¡Lo último en tecnología y gestión!</span></div>

    <div class="container mt-4">
        <!-- Hero Parallax -->
        <div class="featured-post mb-4">
            <div class="featured-overlay">
                <h2 class="display-5"><?= htmlspecialchars($featuredPost['title']) ?></h2>
                <p><?= htmlspecialchars($featuredPost['excerpt']) ?></p>
                <p><small>Por <?= htmlspecialchars($featuredPost['author']) ?> el <?= (new DateTime($featuredPost['date']))->format('d/m/Y') ?></small></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar noticias...">
                </div>
                <!-- Filtros -->
                <div id="filter-buttons" class="mb-3">
                    <button class="btn btn-sm btn-outline-primary active" data-cat="all">Todas</button>
                    <button class="btn btn-sm btn-outline-primary" data-cat="Gestión de Inventario">Inventario</button>
                    <button class="btn btn-sm btn-outline-primary" data-cat="Software">Software</button>
                    <button class="btn btn-sm btn-outline-primary" data-cat="Hardware">Hardware</button>
                </div>

                <h3 class="mb-3">Últimas Noticias</h3>
                <div id="posts-container">
                    <?php foreach ($posts as $post):
                        $diffDays = $today->diff(new DateTime($post['date']))->days;
                        $percent  = round($diffDays / $maxDiff * 100);
                    ?>
                        <div class="card mb-3 fade-in" data-category="<?= htmlspecialchars($post['category']) ?>">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?= $post['image'] ?>" class="img-fluid rounded-start post-card-img" alt="">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <strong class="d-inline-block mb-2 text-primary"><?= htmlspecialchars($post['category']) ?></strong>
                                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                        <p class="card-text text-muted mb-1"><small>Por <?= htmlspecialchars($post['author']) ?> - <?= (new DateTime($post['date']))->format('d/m/Y') ?></small></p>
                                        <p class="card-text"><?= htmlspecialchars($post['excerpt']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación (simulada) -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Sidebar Estático -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 80px;">
                    <div class="p-4 mb-3 rounded shadow-sm bg-white">
                        <h4 class="fst-italic">Acerca de</h4>
                        <p class="mb-0">CMDB System es una solución para centralizar y gestionar todos los activos de TI de tu organización.</p>
                    </div>
                    <div class="p-4 rounded shadow-sm bg-white">
                        <h4 class="fst-italic">Publicidad</h4>
                        <div class="p-3 my-3 bg-secondary-subtle text-center rounded">
                            <p class="mb-0">¿Quieres anunciarte?</p>
                            <p class="mb-0"><small>¡Hazlo aquí!</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts -->
    <div class="toast-container"></div>

    <!-- Botón Scroll-to-Top -->
    <button id="scrollTopBtn"><i class="bi bi-arrow-up"></i></button>

    <!-- Modal: Centro de Notificaciones -->
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationsModalLabel">Centro de Notificaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group" id="notificationsList"></ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> CMDB System. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Estado de notificaciones
            let notifications = [];
            const badge = document.getElementById('notificationCount');
            const list = document.getElementById('notificationsList');
            const toasts = document.querySelector('.toast-container');

            function updateBadge() {
                badge.textContent = notifications.length;
                badge.style.display = notifications.length ? 'inline-block' : 'none';
            }

            function launchConfetti() {
                for (let i = 0; i < 30; i++) {
                    const c = document.createElement('div');
                    c.className = 'confetti-piece';
                    const x = Math.random() * window.innerWidth;
                    const delay = Math.random() * 1.5;
                    const size = 3 + Math.random() * 7;
                    const hue = Math.floor(Math.random() * 360);
                    c.style.left = x + 'px';
                    c.style.width = size + 'px';
                    c.style.height = (size * 1.6) + 'px';
                    c.style.background = `hsl(${hue},100%,50%)`;
                    c.style.animationDelay = delay + 's';
                    document.body.append(c);
                    setTimeout(() => c.remove(), 2500);
                }
            }

            function addNotification(msg) {
                const time = new Date().toLocaleTimeString('es-ES');
                notifications.unshift({
                    msg,
                    time
                });
                updateBadge();
                // Modal list
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = `[${time}] ${msg}`;
                list.prepend(li);
                // Toast
                const toastEl = document.createElement('div');
                toastEl.className = 'toast align-items-center text-bg-primary border-0 mb-2';
                toastEl.role = 'alert';
                toastEl.ariaLive = 'assertive';
                toastEl.ariaAtomic = 'true';
                toastEl.innerHTML = `
              <div class="d-flex">
                <div class="toast-body">${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>`;
                toasts.append(toastEl);
                new bootstrap.Toast(toastEl, {
                    delay: 5000
                }).show();
                launchConfetti();
            }
            // Notificación inicial
            addNotification('🎉 Bienvenido a CMDB System!');

            // Fade-in cards
            const observer = new IntersectionObserver((es) => {
                es.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        observer.unobserve(e.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

            // Filtros
            const buttons = document.querySelectorAll('#filter-buttons .btn');
            const cards = document.querySelectorAll('#posts-container .card');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    buttons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const cat = btn.dataset.cat;
                    cards.forEach(c => {
                        c.style.display = (cat === 'all' || c.dataset.category === cat) ? 'block' : 'none';
                    });
                });
            });

            // Búsqueda con debounce
            let searchTimer;
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', e => {
                clearTimeout(searchTimer);
                const q = e.target.value.trim().toLowerCase();
                cards.forEach(c => {
                    const t = c.querySelector('.card-title').textContent.toLowerCase();
                    const x = c.querySelector('.card-text').textContent.toLowerCase();
                    c.style.display = (t.includes(q) || x.includes(q)) ? '' : 'none';
                });
                searchTimer = setTimeout(() => {}, 500);
            });

            // Ticker dinámico
            const ticks = [
                'Oferta: 50% en licencias de software hasta 31/07',
                'Webinar gratis: Gestión avanzada de CMDB el 30/07',
                'Nuevo blog: SSD vs NVMe – lo que debes saber'
            ];
            let ti = 0;
            setInterval(() => {
                document.getElementById('ticker-text').textContent = ticks[ti++ % ticks.length];
            }, 15000);

            // SSE simulado
            const simPosts = [{
                    title: '¡CMDB 2.0 más rápido!',
                    cat: 'Software',
                    author: 'Equipo CMDB',
                    image: 'https://plus.unsplash.com/premium_vector-1733436206550-79822a7cfb45?q=80&w=2148&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    excerpt: 'Novedades de la versión 2.0.'
                },
                {
                    title: 'Buenas prácticas en TI',
                    cat: 'Gestión de Inventario',
                    author: 'Miguel S.',
                    image: 'https://plus.unsplash.com/premium_vector-1682309080127-19d3a6214a17?q=80&w=2340&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    excerpt: 'Optimiza tus procesos de inventario.'
                },
                {
                    title: 'Ranking 4K 2025',
                    cat: 'Hardware',
                    author: 'Laura M.',
                    image: 'https://images.unsplash.com/photo-1616763355548-1b606f439f86?q=80&w=3270&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    excerpt: 'Comparativa definitiva de pantallas 4K.'
                }
            ];
            let simIdx = 0;
            setInterval(() => {
                if (simIdx >= simPosts.length) return;
                const p = simPosts[simIdx++];
                // Crear card idéntico al resto
                const div = document.createElement('div');
                div.className = 'card mb-3 fade-in';
                div.dataset.category = p.cat;
                div.innerHTML = `
              <div class="row g-0">
                <div class="col-md-4">
                  <img src="${p.image}" class="img-fluid rounded-start post-card-img">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <strong class="d-inline-block mb-2 text-primary">${p.cat}</strong>
                    <h5 class="card-title">${p.title}</h5>
                    <p class="card-text text-muted mb-1"><small>Por ${p.author} - ${new Date().toLocaleDateString('es-ES')}</small></p>
                    <p class="card-text">${p.excerpt}</p>
                  </div>
                </div>
              </div>`;
                document.getElementById('posts-container').prepend(div);
                observer.observe(div);
                addNotification(`Nueva noticia: ${p.title}`);
            }, 20000);

            // Scroll progress & Scroll-to-top
            window.addEventListener('scroll', () => {
                const prog = window.scrollY / (document.body.scrollHeight - window.innerHeight) * 100;
                document.getElementById('scrollProgress').style.width = prog + '%';
                const btn = document.getElementById('scrollTopBtn');
                btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
            });
            document.getElementById('scrollTopBtn').addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>