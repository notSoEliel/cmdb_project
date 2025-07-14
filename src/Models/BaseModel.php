<?php

namespace App\Models;

use App\Core\Database;

/**
 * Clase Abstracta BaseModel
 * Proporciona la funcionalidad CRUD básica y reutilizable, incluido el ordenamiento.
 */
abstract class BaseModel {
    /**
     * @var string El nombre de la tabla en la base de datos. Debe ser definido en la clase hija.
     */
    protected $tableName;

    /**
     * @var array Columnas permitidas para el ordenamiento. Debe ser definido en la clase hija.
     */
    protected $allowedSortColumns = ['id'];

    /**
     * Obtiene el total de registros en la tabla.
     *
     * @return int El total de registros.
     */
    public function countAll() {
        $sql = "SELECT COUNT(id) as total FROM {$this->tableName}";
        $result = Database::getInstance()->query($sql)->find();
        return $result['total'];
    }

    /**
     * Obtiene todos los registros de la tabla, con opciones de ordenamiento.
     *
     * @param array $options Opciones para la consulta, como ['sort' => 'columna', 'order' => 'asc|desc']
     * @return array Un arreglo con todos los registros.
     */
    public function findAll(array $options = []) {
        // --- Lógica de Ordenamiento Reutilizable ---
        $sortColumn = 'id'; // Columna por defecto
        $sortOrder = 'ASC';  // Orden por defecto

        // Validar la columna de ordenamiento
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = $options['sort'];
        }

        // Validar la dirección del ordenamiento
        if (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) {
            $sortOrder = strtoupper($options['order']);
        }
        
        $sql = "SELECT * FROM {$this->tableName} ORDER BY {$sortColumn} {$sortOrder}";
        
        return Database::getInstance()->query($sql)->get();
    }

    /**
     * Busca un registro por su ID.
     *
     * @param int $id El ID del registro.
     * @return mixed
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->tableName} WHERE id = :id";
        return Database::getInstance()->query($sql, ['id' => $id])->find();
    }

    /**
     * Elimina un registro por su ID.
     *
     * @param int $id El ID del registro a eliminar.
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        Database::getInstance()->query($sql, ['id' => $id]);
        return true;
    }
}