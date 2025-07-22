<h1 class="mb-4">Dashboard</h1>
<div class="row">
    <div class="col-lg-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Equipos en Inventario</h5>
                        <p class="card-text fs-1 fw-bold"><?= $totalEquipos ?></p>
                    </div>
                    <i class="bi bi-hdd-stack" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Colaboradores</h5>
                        <p class="card-text fs-1 fw-bold"><?= $totalColaboradores ?></p>
                    </div>
                    <i class="bi bi-people" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Categorías</h5>
                        <p class="card-text fs-1 fw-bold"><?= $totalCategorias ?></p>
                    </div>
                    <i class="bi bi-tags" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>