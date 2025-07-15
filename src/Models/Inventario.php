<?php

namespace App\Models;

use App\Core\Database;

class Inventario extends BaseModel
{
    protected $tableName = 'inventario';
    protected $tableAlias = 'i';

    /**
     * @var array Columnas permitidas para el ordenamiento.
     * Se especifican con su alias de tabla para evitar ambigüedades.
     */
    protected $allowedSortColumns = [
        'i.id',
        'i.nombre_equipo',
        'i.marca',
        'i.costo',
        'i.fecha_ingreso',
        'nombre_categoria',
        'nombre_colaborador',
        'i.estado'
    ];

    protected $searchableColumns = ['i.nombre_equipo', 'i.marca', 'i.modelo', 'i.serie', 'co.nombre', 'co.apellido'];

    /**
     * Cuenta los registros filtrados, corrigiendo la duplicación por JOIN.
     */
    public function countFiltered(array $options = []): int
    {
        $sql = "SELECT COUNT(DISTINCT i.id) as total
                FROM {$this->tableName} i
                LEFT JOIN categorias c ON i.categoria_id = c.id
                LEFT JOIN asignaciones a ON i.id = a.inventario_id AND a.fecha_devolucion IS NULL
                LEFT JOIN colaboradores co ON a.colaborador_id = co.id";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result['total'] ?? 0;
    }

    /**
     * Busca todos los registros, corrigiendo la duplicación y el ordenamiento.
     */
    public function findAll(array $options = []): array
    {
        $sortColumn = "i.id"; // Valor por defecto
        // Ahora se comprueba que el sort solicitado esté en la lista de columnas permitidas explícitas.
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';

        $perPage = (int)($options['perPage'] ?? 10);
        $page = (int)($options['page'] ?? 1);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT i.*,
                       c.nombre as nombre_categoria,
                       CONCAT(co.nombre, ' ', co.apellido) as nombre_colaborador,
                       a.id as asignacion_id,
                       ii.ruta_imagen as thumbnail_path
                FROM {$this->tableName} i
                LEFT JOIN categorias c ON i.categoria_id = c.id
                LEFT JOIN inventario_imagenes ii ON i.id = ii.inventario_id AND ii.es_thumbnail = 1
                LEFT JOIN asignaciones a ON a.id = (
                    SELECT MAX(id) FROM asignaciones 
                    WHERE inventario_id = i.id AND fecha_devolucion IS NULL
                )
                LEFT JOIN colaboradores co ON a.colaborador_id = co.id";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $sql .= " ORDER BY {$sortColumn} {$sortOrder} LIMIT {$perPage} OFFSET {$offset}";

        return Database::getInstance()->query($sql, $params)->get();
    }

    public function save($data)
    {
        $costo = !empty($data['costo']) ? $data['costo'] : 0.00;
        $depreciacion = !empty($data['tiempo_depreciacion_anios']) ? $data['tiempo_depreciacion_anios'] : 0;
        $fecha_ingreso = !empty($data['fecha_ingreso']) ? $data['fecha_ingreso'] : null;

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
            // 'estado' => $data['estado'] ?? 'En Stock', <-- LÍNEA ELIMINADA
        ];

        if (isset($data['id']) && !empty($data['id'])) {
            $params['id'] = $data['id'];
            // Se quita 'estado' de la consulta UPDATE
            $sql = "UPDATE {$this->tableName} SET
                        nombre_equipo = :nombre_equipo, marca = :marca, modelo = :modelo, serie = :serie,
                        costo = :costo, fecha_ingreso = :fecha_ingreso,
                        tiempo_depreciacion_anios = :tiempo_depreciacion_anios,
                        categoria_id = :categoria_id
                    WHERE id = :id";
        } else {
            // En la creación, sí se debe incluir el estado por defecto.
            $params['estado'] = 'En Stock';
            $sql = "INSERT INTO {$this->tableName} (nombre_equipo, marca, modelo, serie, costo, fecha_ingreso, tiempo_depreciacion_anios, categoria_id, estado)
                    VALUES (:nombre_equipo, :marca, :modelo, :serie, :costo, :fecha_ingreso, :tiempo_depreciacion_anios, :categoria_id, :estado)";
        }
        Database::getInstance()->query($sql, $params);
        return true;
    }
}
