<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Inventario
 *
 * Gestiona todas las operaciones de la base de datos para la tabla 'inventario'.
 * Extiende de BaseModel para heredar funcionalidades comunes, pero sobrescribe
 * métodos como findAll() y countFiltered() para añadir el JOIN con la tabla de categorías.
 */
class Inventario extends BaseModel
{
    /**
     * @var string El nombre de la tabla en la base de datos.
     */
    protected $tableName = 'inventario';

    /**
     * @var string El alias de la tabla usado en consultas con JOINs.
     */
    protected $tableAlias = 'i';

    /**
     * @var array Columnas permitidas para el ordenamiento dinámico.
     */
    protected $allowedSortColumns = ['id', 'nombre_equipo', 'marca', 'serie', 'costo', 'fecha_ingreso'];

    /**
     * @var array Columnas en las que se realizará la búsqueda de texto libre.
     */
    protected $searchableColumns = ['nombre_equipo', 'marca', 'modelo', 'serie'];

    /**
     * Sobrescribe countFiltered para incluir el JOIN y permitir filtrar por categoría.
     *
     * @param array $options Opciones de filtro y búsqueda.
     * @return int El número total de registros que coinciden.
     */
    public function countFiltered(array $options = []): int
    {
        $sql = "SELECT COUNT(DISTINCT i.id) as total
                FROM {$this->tableName} i
                LEFT JOIN categorias c ON i.categoria_id = c.id";

        // Reutiliza la lógica del padre para construir la cláusula WHERE
        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result['total'] ?? 0;
    }

    /**
     * Sobrescribe findAll para incluir el JOIN con categorías y obtener el nombre.
     *
     * @param array $options Opciones de filtro, búsqueda, ordenamiento y paginación.
     * @return array La lista de registros de inventario.
     */
    public function findAll(array $options = []): array
    {
        // --- Lógica de Ordenamiento y Paginación (sin cambios) ---
        $sortColumn = "i.id";
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = "i." . $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';
        $perPage = $options['perPage'] ?? 10;
        $page = $options['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        // --- INICIO DE LA MODIFICACIÓN DE LA CONSULTA ---
        $sql = "SELECT i.*, c.nombre as nombre_categoria, ii.ruta_imagen as thumbnail_path
                FROM {$this->tableName} i
                LEFT JOIN categorias c ON i.categoria_id = c.id
                LEFT JOIN inventario_imagenes ii ON i.id = ii.inventario_id AND ii.es_thumbnail = 1";
        // --- FIN DE LA MODIFICACIÓN ---

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $sql .= " ORDER BY {$sortColumn} {$sortOrder} LIMIT {$perPage} OFFSET {$offset}";
        return Database::getInstance()->query($sql, $params)->get();
    }

    /**
     * Guarda (Crea o Actualiza) un registro de inventario.
     * Es específico de este modelo por sus columnas y la sanitización de datos.
     *
     * @param array $data Los datos del equipo a guardar.
     * @return bool True si la operación fue exitosa.
     * @throws \Exception Si la fecha de ingreso es inválida.
     */
    public function save($data)
    {
        // Sanitiza los datos numéricos y de fecha antes de guardarlos.
        $costo = !empty($data['costo']) ? $data['costo'] : 0.00;
        $depreciacion = !empty($data['tiempo_depreciacion_anios']) ? $data['tiempo_depreciacion_anios'] : 0;
        $fecha_ingreso = !empty($data['fecha_ingreso']) ? $data['fecha_ingreso'] : null;

        // Validación del lado del servidor para asegurar que la fecha no sea nula.
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
