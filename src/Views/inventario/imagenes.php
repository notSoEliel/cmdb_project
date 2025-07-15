<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Imágenes para <?= htmlspecialchars($equipo['nombre_equipo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .thumbnail-highlight {
            border: 4px solid #0d6efd;
            /* Borde azul para la miniatura seleccionada */
        }

        .image-container {
            aspect-ratio: 1 / 1;
            /* Forza a que el contenedor sea un cuadrado perfecto */
            background-color: #f8f9fa;
            /* Fondo gris claro por defecto */
            border-radius: .5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.2s ease-in-out;
            /* Transición suave para el hover */
            overflow: hidden;
            /* Esconde cualquier parte de la imagen que se desborde */
        }

        .image-container:hover {
            background-color: #e9ecef;
            /* Fondo gris más oscuro al pasar el mouse */
        }

        .image-container img {
            width: 100%;
            height: 100%;
            /* Esta es la propiedad clave: ajusta la imagen sin recortarla */
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <a href="index.php?route=inventario">← Volver al Inventario</a>
        <h1 class="mt-2">Imágenes para: <small class="text-muted"><?= htmlspecialchars($equipo['nombre_equipo']) ?></small></h1>
        <?php $mensaje = $_GET['mensaje'] ?? '';
        if ($mensaje): ?><div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">➕ Subir Nueva Imagen</div>
            <div class="card-body">
                <form action="index.php?route=inventario&action=uploadImage" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="inventario_id" value="<?= $equipo['id'] ?>">
                    <div class="input-group">
                        <input type="file" class="form-control" name="imagen" required>
                        <button class="btn btn-primary" type="submit">Subir Imagen</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if (empty($imagenes)): ?>
                <p class="text-center">Este equipo no tiene imágenes.</p>
                <?php else: foreach ($imagenes as $img): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card <?= $img['es_thumbnail'] ? 'thumbnail-highlight' : '' ?>">
                            <div class="image-container">
                                <img src="<?= BASE_URL . 'uploads/inventario/' . htmlspecialchars($img['ruta_imagen']) ?>" alt="Imagen del equipo">
                            </div>
                            <div class="card-body text-center">
                                <div class="btn-group">
                                    <form action="index.php?route=inventario&action=setThumbnail" method="post" class="d-inline">
                                        <input type="hidden" name="inventario_id" value="<?= $equipo['id'] ?>">
                                        <input type="hidden" name="imagen_id" value="<?= $img['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" <?= $img['es_thumbnail'] ? 'disabled' : '' ?>>Principal</button>
                                    </form>
                                    <form action="index.php?route=inventario&action=destroyImage" method="post" class="d-inline">
                                        <input type="hidden" name="inventario_id" value="<?= $equipo['id'] ?>">
                                        <input type="hidden" name="imagen_id" value="<?= $img['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</body>

</html>