<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Categoria
 * Hereda de BaseModel y define la lógica específica para las categorías.
 */
class Categoria extends BaseModel {
    /**
     * @var string El nombre de la tabla asociada a este modelo.
     */
    protected $tableName = 'categorias';

    /**
     * @var array Columnas por las que se permite ordenar en esta tabla.
     */
    protected $allowedSortColumns = ['id', 'nombre'];

    /**
     * @var array Columnas en las que se realizará la búsqueda de texto libre.
     */
    protected $searchableColumns = ['nombre'];
    
    /**
     * Guarda una categoría (la crea si no tiene ID, la actualiza si lo tiene).
     *
     * @param array $data Los datos de la categoría ['nombre', 'id' (opcional)].
     * @return bool True si la operación fue exitosa.
     */
    public function save($data) {
        if (isset($data['id']) && !empty($data['id'])) {
            // Actualizar
            $sql = "UPDATE {$this->tableName} SET nombre = :nombre WHERE id = :id";
            $params = ['id' => $data['id'], 'nombre' => $data['nombre']];
        } else {
            // Crear
            $sql = "INSERT INTO {$this->tableName} (nombre) VALUES (:nombre)";
            $params = ['nombre' => $data['nombre']];
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }
}