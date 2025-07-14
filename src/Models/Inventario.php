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
        // Sanitizamos los datos numéricos opcionales.
        $costo = !empty($data['costo']) ? $data['costo'] : 0.00;
        $depreciacion = !empty($data['tiempo_depreciacion_anios']) ? $data['tiempo_depreciacion_anios'] : 0;

        // Si la fecha de ingreso está vacía (aunque no debería por la validación), le asignamos NULL.
        // Asegúrate de que tu columna 'fecha_ingreso' en la BD permita NULL si no es obligatoria,
        // o mantenla como está si siempre será requerida.
        $fecha_ingreso = !empty($data['fecha_ingreso']) ? $data['fecha_ingreso'] : null;

        // Si la fecha es NULA y el campo es obligatorio en la BD, MySQL dará error.
        // Esto es bueno, es una capa extra de seguridad de datos.
        if ($fecha_ingreso === null) {
            throw new \Exception("La fecha de ingreso no puede estar vacía.");
        }

        $params = [
            'nombre_equipo' => $data['nombre_equipo'],
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'serie' => $data['serie'],
            'costo' => $costo,
            'fecha_ingreso' => $fecha_ingreso,
            'tiempo_depreciacion_anios' => $depreciacion,
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
