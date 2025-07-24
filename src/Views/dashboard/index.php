<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <style>
        /* <<< INICIO CAMBIO: CSS — Eliminar modo oscuro y ajustar alturas >>> */
        /* Variables de color (mantener sin tema oscuro) */
        :root {
            --bg: #f8f9fc;
            --fg: #343a40;
            --card: #ffffff;
            --primary: #0d6efd;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --secondary: #6c757d;
            /* Colores adicionales para el Dashboard */
            --purple: #6f42c1;
            --orange: #fd7e14;
            --dark-blue: #2c3e50; /* bg-primary-dark anterior */
            --dark-green: #1e8449; /* bg-success-dark anterior */
        }
        /* ELIMINAR COMPLETAMENTE EL BLOQUE [data-theme="dark"] */
        /* [data-theme="dark"] {
            --bg: #121416;
            --fg: #e4e6eb;
            --card: #1e1e26;
        } */
        html { background: var(--bg); color: var(--fg); transition: .3s; }
        body { font-family: 'Segoe UI', sans-serif; }

        /* Header (sin cambios, solo se elimina el toggle HTML) */
        .header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        /* ELIMINAR .theme-toggle { cursor: pointer; font-size: 1.5rem; } */

        /* Grid de KPIs (ajustar responsividad y tamaño de fuente) */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background: var(--card);
            border-radius: .75rem;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            transition: transform .3s;
        }
        .kpi-card:hover { transform: translateY(-5px); }
        .kpi-title { font-size: .9rem; text-transform: uppercase; font-weight:600; margin-bottom:.5rem; }
        .kpi-value { font-size:2.5rem; font-weight:bold; }
        .kpi-icon {
            position:absolute; top:-10px; right:-10px;
            font-size:5rem; opacity:.05;
        }
        @media(max-width:768px){
            .kpi-value{ font-size:2rem }
            .kpi-title{ font-size:.8rem }
        }

        /* Contenedor de gráficas (Ajustar altura para evitar "infinito") */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media(max-width:992px){
            .charts-container { grid-template-columns:1fr; }
        }
        .chart-card {
            background: var(--card);
            border-radius:.75rem;
            padding:1rem;
            box-shadow:0 2px 6px rgba(0,0,0,.1);
            /* ELIMINAR height:100%; que causaba el estiramiento */
            /* height:100%; */
            min-height: 350px; /* Asegurar una altura mínima para la card */
        }
        .chart-title {
            font-weight:600;
            margin-bottom:1rem;
            text-align:center;
        }
        .chart-card canvas {
            width:100% !important; /* Forzar el ancho del canvas */
            height:250px !important; /* Forzar la altura del canvas */
            max-height: 250px; /* Asegurar que no se exceda */
        }

        /* Gauge completo (mantener, ya tenía altura fija) */
        #availabilityGauge { height:300px !important; }

        /* Animación count-up (sin cambios) */
        .count-up { visibility:hidden; }
        /* <<< FIN CAMBIO: CSS >>> */
    </style>
</head>

<div class="container py-4">
        <div class="header">
            <h1>Panel General de Equipos</h1>
            </div>

        <h3 class="mb-3">Inventario Global</h3>
        <hr>
        <div class="grid-cards">
            <?php
                $kpi_cards = [
                    ["title"=>"Total Equipos","value"=>$totalInventarioGeneral,"icon"=>"bi-box-seam","color"=>"var(--dark-blue)"],
                    ["title"=>"Asignados","value"=>$totalAsignados,"icon"=>"bi-people-fill","color"=>"var(--primary)"],
                    ["title"=>"Disponibles","value"=>$totalDisponibles,"icon"=>"bi-hdd-stack-fill","color"=>"var(--success)"],
                    ["title"=>"En Reparación/Dañados","value"=>$totalEnReparacionDañado,"icon"=>"bi-exclamation-triangle-fill","color"=>"var(--warning)"],
                    ["title"=>"Expirados","value"=>$totalExpirados,"icon"=>"bi-hourglass-split","color"=>"var(--danger)"],
                    ["title"=>"Por Expirar (6M)","value"=>$totalPorExpirar,"icon"=>"bi-hourglass-top","color"=>"var(--orange)"],
                    ["title"=>"Donados","value"=>$totalDonados,"icon"=>"bi-gift-fill","color"=>"var(--secondary)"],
                    ["title"=>"Descartados","value"=>$totalDescartados,"icon"=>"bi-trash-fill","color"=>"var(--dark-green)"],
                ];
                foreach($kpi_cards as $c){
                    echo "<div class='kpi-card'>
                            <div class='kpi-title'>{$c['title']}</div>
                            <div class='kpi-value count-up' data-target='{$c['value']}'>0</div>
                            <i class='bi {$c['icon']}' style='color:{$c['color']}'></i>
                        </div>";
                }
            ?>
        </div>

        <h3 class="mb-3">Usuarios y Organización</h3>
        <hr>
        <div class="grid-cards" style="grid-template-columns: repeat(auto-fit,minmax(250px,1fr));">
            <?php
                $org_cards = [
                    ["title"=>"Total Colaboradores","value"=>$totalColaboradores,"icon"=>"bi-person-workspace","color"=>"var(--primary)"],
                    ["title"=>"Total Categorías","value"=>$totalCategorias,"icon"=>"bi-tags-fill","color"=>"var(--info)"],
                    ["title"=>"Usuarios Administradores","value"=>$totalAdminUsers,"icon"=>"bi-person-gear","color"=>"var(--dark-blue)"], // Asumiendo que $totalAdminUsers se pasará
                ];
                foreach($org_cards as $c){
                    echo "<div class='kpi-card'>
                            <div class='kpi-title'>{$c['title']}</div>
                            <div class='kpi-value count-up' data-target='{$c['value']}'>0</div>
                            <i class='bi {$c['icon']}' style='color:{$c['color']}'></i>
                        </div>";
                }
            ?>
        </div>

        <h3 class="mb-3">Análisis Visual</h3>
        <hr>
        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-title">Distribución de Equipos por Estado</div>
                <canvas id="inventoryChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">Estado de Solicitudes</div>
                <canvas id="requestsChart"></canvas>
            </div>
        </div>

        <div class="chart-card mb-4" style="grid-column: 1 / -1;"> <div class="chart-title">Porcentaje de Equipos Disponibles</div>
            <canvas id="availabilityGauge"></canvas>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // <<< INICIO CAMBIO: Script JavaScript — Eliminar themeToggle y actualizar datos de gráficas >>>
        // ELIMINAR COMPLETAMENTE EL BLOQUE themeToggle
        /*
        const toggle = document.getElementById('themeToggle');
        toggle.addEventListener('click', () => {
            document.documentElement.toggleAttribute('data-theme','dark');
        });
        */

        // — Count-up usando IntersectionObserver (Sin cambios)
        document.querySelectorAll('.count-up').forEach(el => {
            const obs = new IntersectionObserver((entries,o) => {
                if(entries[0].isIntersecting){
                    let target = +el.dataset.target, count = 0;
                    const step = target/200;
                    el.style.visibility='visible';
                    const iv = setInterval(()=>{
                        count+=step;
                        if(count>=target){
                            el.textContent=target; clearInterval(iv);
                        } else el.textContent=Math.floor(count);
                    },10);
                    o.unobserve(el);
                }
            },{threshold:0.75});
            obs.observe(el);
        });

        // — Datos en JS (Actualizados con todas las nuevas métricas)
        const totals = {
            asignados: <?= $totalAsignados ?>,
            disponibles: <?= $totalDisponibles ?>,
            reparacion: <?= $totalEnReparacionDañado ?>,
            donados: <?= $totalDonados ?>,
            descartados: <?= $totalDescartados ?>,
            expirados: <?= $totalExpirados ?>,
            porExpirar: <?= $totalPorExpirar ?>,
            generales: <?= $totalInventarioGeneral ?>,
            solicitudesPendientes: <?= $solicitudesPendientes ?>,
            solicitudesCompletadas: <?= $solicitudesCompletadas ?>,
        };

        // — Dona Inventario (Ahora incluye expirados y por expirar)
        new Chart(document.getElementById('inventoryChart'), {
            type:'doughnut',
            data:{
                labels:['Asignados','Disponibles','Reparación/Dañados','Donados','Descartados','Expirados','Por Expirar'],
                datasets:[{
                    data:[
                        totals.asignados,
                        totals.disponibles,
                        totals.reparacion,
                        totals.donados,
                        totals.descartados,
                        totals.expirados,
                        totals.porExpirar
                    ],
                    backgroundColor:[
                        getStyle('--primary'),
                        getStyle('--success'),
                        getStyle('--warning'),
                        getStyle('--secondary'),
                        getStyle('--danger'),
                        getStyle('--purple'),
                        getStyle('--orange')
                    ],
                    hoverOffset:10
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{ legend:{position:'right', labels:{padding:20} } }
            }
        });

        // — Barras Solicitudes (Más claro, y ahora con datos de la vista)
        new Chart(document.getElementById('requestsChart'), {
            type:'bar',
            data:{
                labels:['Pendientes','Completadas','Rechazadas'], // Añadir 'Rechazadas' si se quiere
                datasets:[{
                    label:'Solicitudes',
                    data:[ totals.solicitudesPendientes, totals.solicitudesCompletadas, <?= $solicitudesRechazadas ?? 0 ?> ], // Asumiendo $solicitudesRechazadas se pasa
                    backgroundColor:[ getStyle('--warning'), getStyle('--success'), getStyle('--danger') ]
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                scales:{ y:{ beginAtZero:true } },
                plugins:{ legend:{ display:false } }
            }
        });

        // — Gauge Disponibilidad (Utiliza los nuevos totales)
        const availablePercentage = totals.generales > 0 ? (totals.disponibles / totals.generales * 100).toFixed(1) : 0;
        new Chart(document.getElementById('availabilityGauge'), {
            type:'doughnut',
            data:{
                labels:['Disponibles','Resto'],
                datasets:[{
                    data:[ totals.disponibles, totals.generales - totals.disponibles ],
                    backgroundColor:[ getStyle('--success'), getStyle('--secondary') ]
                }]
            },
            options:{
                cutout:'80%',
                rotation:-90, // Rotar para que la barra empiece abajo
                circumference:180, // Solo la mitad inferior
                plugins:{
                    legend:{ display:false },
                    tooltip:{ enabled:false },
                    // Texto central del gauge
                    title:{
                        display:true,
                        text: availablePercentage + '%', // Muestra el porcentaje
                        position: 'center',
                        font:{ size:24, weight:'bold' }
                    }
                }
            }
        });

        // — Helpers (Sin cambios)
        function getStyle(varName){
            return getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
        }
        // <<< FIN CAMBIO: Script JavaScript >>>
    </script>