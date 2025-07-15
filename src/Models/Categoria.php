<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Categoria
 * Hereda de BaseModel y define la lógica específica para las categorías.
 */
class Categoria extends BaseModel
{
    protected $tableName = 'categorias';
    protected $tableAlias = 'c';
    protected $allowedSortColumns = ['c.id', 'c.nombre'];
    protected $searchableColumns = ['nombre'];

    /**
     * Guarda una categoría (la crea si no tiene ID, la actualiza si lo tiene).
     *
     * @param array $data Los datos de la categoría ['nombre', 'id' (opcional)].
     * @return bool True si la operación fue exitosa.
     */
public function save($data)
    {
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        // --- VALIDACIÓN PREVIA ---
        // Verifica si ya existe otra categoría con el mismo nombre.
        if ($this->exists('nombre', $data['nombre'], $currentId)) {
            throw new \Exception("La categoría '{$data['nombre']}' ya existe.");
        }
        // --- FIN DE VALIDACIÓN ---

        if ($currentId) {
            // Actualizar
            $sql = "UPDATE {$this->tableName} SET nombre = :nombre WHERE id = :id";
            $params = ['id' => $currentId, 'nombre' => $data['nombre']];
        } else {
            // Crear
            $sql = "INSERT INTO {$this->tableName} (nombre) VALUES (:nombre)";
            $params = ['nombre' => $data['nombre']];
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }
}
