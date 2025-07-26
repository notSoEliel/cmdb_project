<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo InventarioImagen
 * Gestiona las operaciones de la base de datos para las imágenes del inventario.
 */
class InventarioImagen
{

    /**
     * Busca todas las imágenes asociadas a un ID de inventario.
     * @param int $inventario_id El ID del equipo del inventario.
     * @return array
     */
    public function findByInventarioId($inventario_id)
    {
        $sql = "SELECT * FROM inventario_imagenes WHERE inventario_id = :inventario_id ORDER BY es_thumbnail DESC";
        return Database::getInstance()->query($sql, ['inventario_id' => $inventario_id])->get();
    }

    /**
     * Guarda una nueva referencia de imagen en la base de datos.
     * @param int $inventario_id
     * @param string $ruta_imagen
     * @return bool
     */
    public function save($inventario_id, $ruta_imagen, bool $es_thumbnail = false)
    {
        $sql = "INSERT INTO inventario_imagenes (inventario_id, ruta_imagen, es_thumbnail) VALUES (:inventario_id, :ruta_imagen, :es_thumbnail)";
        Database::getInstance()->query($sql, ['inventario_id' => $inventario_id, 'ruta_imagen' => $ruta_imagen, 'es_thumbnail' => (int)$es_thumbnail]);
        return Database::getInstance()->lastInsertId();
    }

    /**
     * Busca una imagen por su propio ID.
     * @param int $id
     * @return mixed
     */
    public function findById($id)
    {
        return Database::getInstance()->query("SELECT * FROM inventario_imagenes WHERE id = :id", ['id' => $id])->find();
    }

    /**
     * Elimina una imagen de la base de datos por su ID.
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        Database::getInstance()->query("DELETE FROM inventario_imagenes WHERE id = :id", ['id' => $id]);
        return true;
    }

    /**
     * Establece una imagen como la principal (thumbnail).
     * Primero resetea todas las imágenes para un equipo y luego establece la seleccionada.
     * @param int $inventario_id
     * @param int $imagen_id
     * @return bool
     */
    public function setThumbnail($inventario_id, $imagen_id)
    {
        $db = Database::getInstance();
        // 1. Quitar cualquier otro thumbnail para este equipo
        $db->query("UPDATE inventario_imagenes SET es_thumbnail = 0 WHERE inventario_id = :inventario_id", ['inventario_id' => $inventario_id]);
        // 2. Establecer el nuevo thumbnail
        $db->query("UPDATE inventario_imagenes SET es_thumbnail = 1 WHERE id = :id AND inventario_id = :inventario_id", ['id' => $imagen_id, 'inventario_id' => $inventario_id]);
        return true;
    }
}
