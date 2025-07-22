<h1 class="mb-4">Portal del Colaborador</h1>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">¡Bienvenido, <?= htmlspecialchars($_SESSION['user_nombre'] ?? '') ?>!</h5>
        <p class="card-text">Este es tu portal personal. Desde aquí podrás ver tus equipos asignados y realizar solicitudes.</p>
        </div>
</div>