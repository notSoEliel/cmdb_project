<?php

namespace App\Models;

use App\Core\Database;

class Inventario extends BaseModel
{

    protected $tableName = 'inventario';
    protected $allowedSortColumns = ['id', 'nombre_equipo', 'marca', 'serie', 'costo', 'fecha_ingreso'];

    // Sobrescribimos findAll para incluir el nombre de la categoría
    public function findAll(array $options = [])
    {
        $sortColumn = 'i.id'; // 'i' es el alias para la tabla inventario
        $sortOrder = 'ASC';

        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = 'i.' . $options['sort'];
        }

        if (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) {
            $sortOrder = strtoupper($options['order']);
        }

        // Usamos un LEFT JOIN para unir la tabla de inventario con la de categorías
        $sql = "SELECT i.*, c.nombre as nombre_categoria 
                FROM {$this->tableName} i
                LEFT JOIN categorias c ON i.categoria_id = c.id
                ORDER BY {$sortColumn} {$sortOrder}";

        return Database::getInstance()->query($sql)->get();
    }

    public function save($data)
    {
        $params = [
            'nombre_equipo' => $data['nombre_equipo'],
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'serie' => $data['serie'],
            'costo' => $data['costo'],
            'fecha_ingreso' => $data['fecha_ingreso'],
            'tiempo_depreciacion_anios' => $data['tiempo_depreciacion_anios'],
            'categoria_id' => $data['categoria_id'],
            'estado' => $data['estado'] ?? 'En Stock',
        ];

        if (isset($data['id']) && !empty($data['id'])) {
            $params['id'] = $data['id'];
            $sql = "UPDATE {$this->tableName} SET 
                        nombre_equipo = :nombre_equipo, marca = :marca, modelo = :modelo, serie = :serie, 
                        costo = :costo, fecha_ingreso = :fecha_ingreso, 
                        tiempo_depreciacion_anios = :tiempo_depreciacion_anios, 
                        categoria_id = :categoria_id, estado = :estado
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO {$this->tableName} (nombre_equipo, marca, modelo, serie, costo, fecha_ingreso, tiempo_depreciacion_anios, categoria_id, estado) 
                    VALUES (:nombre_equipo, :marca, :modelo, :serie, :costo, :fecha_ingreso, :tiempo_depreciacion_anios, :categoria_id, :estado)";
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }
}
