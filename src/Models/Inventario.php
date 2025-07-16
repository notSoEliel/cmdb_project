<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Inventario
 *
 * Se encarga de todas las operaciones de la base de datos para los equipos.
 * Define su propia estructura de consulta con JOINs para obtener datos relacionados.
 */
class Inventario extends BaseModel
{
    protected $tableName = 'inventario';
    protected $tableAlias = 'i';

    // Se definen las propiedades que el BaseModel usará para construir la consulta
    public function __construct()
    {
        parent::__construct(); // Llama al constructor del padre
        $this->selectClause = "i.*, 
                               c.nombre as nombre_categoria, 
                               CONCAT(co.nombre, ' ', co.apellido) as nombre_colaborador, 
                               a.id as asignacion_id, 
                               ii.ruta_imagen as thumbnail_path";

        $this->joins = "LEFT JOIN categorias c ON i.categoria_id = c.id
                        LEFT JOIN inventario_imagenes ii ON i.id = ii.inventario_id AND ii.es_thumbnail = 1
                        LEFT JOIN asignaciones a ON a.id = (
                            SELECT MAX(id) FROM asignaciones 
                            WHERE inventario_id = i.id AND fecha_devolucion IS NULL
                        )
                        LEFT JOIN colaboradores co ON a.colaborador_id = co.id";
    }

    protected $allowedSortColumns = [
        'i.id',
        'i.nombre_equipo',
        'nombre_categoria',
        'nombre_colaborador',
        'i.estado'
    ];

    protected $searchableColumns = ['i.nombre_equipo', 'i.marca', 'i.modelo', 'i.serie', 'co.nombre', 'co.apellido'];

    /**
     * Guarda los datos de un equipo.
     * Al actualizar, NO modifica el estado de asignación.
     * Al crear, establece el estado por defecto a "En Stock".
     */
    public function save($data)
    {
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        // Validación de duplicados para el número de serie
        if (!empty($data['serie']) && $this->exists('serie', $data['serie'], $currentId)) {
            throw new \Exception("El número de serie '{$data['serie']}' ya está registrado.");
        }

        $params = [
            'nombre_equipo' => $data['nombre_equipo'],
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'serie' => $data['serie'],
            'costo' => !empty($data['costo']) ? $data['costo'] : 0.00,
            'fecha_ingreso' => !empty($data['fecha_ingreso']) ? $data['fecha_ingreso'] : null,
            'tiempo_depreciacion_anios' => !empty($data['tiempo_depreciacion_anios']) ? $data['tiempo_depreciacion_anios'] : 0,
            'categoria_id' => $data['categoria_id'],
        ];

        if ($currentId) {
            // --- INICIO DE LA MODIFICACIÓN ---
            // MODO UPDATE: Se añaden 'estado' y 'notas_donacion' a los parámetros
            $params['id'] = $currentId;
            $params['estado'] = $data['estado']; // Se toma el estado del formulario
            $params['notas_donacion'] = ($data['estado'] === 'Donado' || $data['estado'] === 'En Descarte') ? ($data['notas_donacion'] ?? null) : null;

            // La consulta UPDATE ahora incluye los campos 'estado' y 'notas_donacion'
            $sql = "UPDATE {$this->tableName} SET
                        nombre_equipo = :nombre_equipo, marca = :marca, modelo = :modelo, serie = :serie,
                        costo = :costo, fecha_ingreso = :fecha_ingreso,
                        tiempo_depreciacion_anios = :tiempo_depreciacion_anios,
                        categoria_id = :categoria_id, estado = :estado, notas_donacion = :notas_donacion
                    WHERE id = :id";
        } else {
            // MODO CREATE: Se establece el estado inicial a "En Stock".
            $params['estado'] = 'En Stock';
            $sql = "INSERT INTO {$this->tableName} (nombre_equipo, marca, modelo, serie, costo, fecha_ingreso, tiempo_depreciacion_anios, categoria_id, estado)
                    VALUES (:nombre_equipo, :marca, :modelo, :serie, :costo, :fecha_ingreso, :tiempo_depreciacion_anios, :categoria_id, :estado)";
        }
        // --- FIN DE LA MODIFICACIÓN ---

        Database::getInstance()->query($sql, $params);
        return true;
    }
}
