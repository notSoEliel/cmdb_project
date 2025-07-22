<a href="index.php?route=necesidades&action=misSolicitudes">← Volver a Mis Solicitudes</a>
<h1 class="mt-2 mb-4"><?= $pageTitle ?? 'Crear Solicitud' ?></h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?route=necesidades&action=store" method="POST">
            <div class="mb-3">
                <label for="descripcion" class="form-label">
                    <h5>¿Qué equipo o software necesitas?</h5>
                </label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="5" 
                          placeholder="Sé lo más descriptivo posible. Incluye marca, modelo o especificaciones si las conoces. (Ej: 'Necesito un monitor Dell de 24 pulgadas' o 'Una licencia para Adobe Photoshop')."
                          required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
        </form>
    </div>
</div>