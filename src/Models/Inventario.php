<?php

namespace App\Models;

use App\Core\Database; // Asegúrate de que esta línea esté presente

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

    protected $allowedSortColumns = [
        'i.id',
        'i.nombre_equipo',
        'nombre_categoria',
        'nombre_colaborador',
        'i.estado',
        'i.fecha_ingreso',
        'fecha_fin_vida'
    ];

    protected $searchableColumns = ['i.nombre_equipo', 'i.marca', 'i.modelo', 'i.serie', 'co.nombre', 'co.apellido'];

    // Se definen las propiedades que el BaseModel usará para construir la consulta
    public function __construct()
    {
        parent::__construct(); // Llama al constructor del padre
        // AÑADIR 'a.fecha_asignacion' AL SELECT
        $this->selectClause = "i.*, 
                               c.nombre as nombre_categoria, 
                               CONCAT(co.nombre, ' ', co.apellido) as nombre_colaborador, 
                               a.id as asignacion_id, 
                               a.fecha_asignacion, -- AÑADIDO: para obtener la fecha de asignación activa
                               ii.ruta_imagen as thumbnail_path,
                               DATE_ADD(i.fecha_ingreso, INTERVAL i.tiempo_depreciacion_anios YEAR) AS fecha_fin_vida";


        $this->joins = "LEFT JOIN categorias c ON i.categoria_id = c.id
                        LEFT JOIN inventario_imagenes ii ON i.id = ii.inventario_id AND ii.es_thumbnail = 1
                        LEFT JOIN asignaciones a ON a.id = (
                            SELECT MAX(id) FROM asignaciones 
                            WHERE inventario_id = i.id AND fecha_devolucion IS NULL
                        )
                        LEFT JOIN colaboradores co ON a.colaborador_id = co.id";
    }

    /**
     * Guarda los datos de un equipo en la base de datos.
     * Asume que las validaciones y la lógica de estado ya se han manejado antes de esta llamada.
     * Este método se enfoca en la persistencia de datos.
     *
     * @param array $data Los datos del equipo a guardar (deben incluir 'serie').
     * @return int El ID del registro insertado/actualizado.
     * @throws \Exception Si la serie ya existe (validación de unicidad).
     */
    public function save(array $data)
    {
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        // **ÚNICA VALIDACIÓN ESPECÍFICA AQUÍ: UNICIDAD DE SERIE**
        // Saneamiento de la serie (trim) antes de la validación y guardar
        $data['serie'] = trim($data['serie'] ?? '');
        // Usamos el método exists() del BaseModel
        if (!empty($data['serie']) && $this->exists('serie', $data['serie'], $currentId)) {
            throw new \Exception("El número de serie '{$data['serie']}' ya está registrado.");
        }

        // Definir los campos que serán mapeados a la base de datos
        // Asegúrate de que todos los campos relevantes estén aquí, incluyendo los nuevos como 'estado' y 'notas_donacion'
        $params = [
            'nombre_equipo' => $data['nombre_equipo'] ?? null,
            'marca' => $data['marca'] ?? null,
            'modelo' => $data['modelo'] ?? null,
            'serie' => $data['serie'], // Ya saneada y validada para unicidad
            'costo' => !empty($data['costo']) ? (float)$data['costo'] : 0.00,
            'fecha_ingreso' => !empty($data['fecha_ingreso']) ? $data['fecha_ingreso'] : date('Y-m-d'),
            'tiempo_depreciacion_anios' => !empty($data['tiempo_depreciacion_anios']) ? (int)$data['tiempo_depreciacion_anios'] : 0,
            'categoria_id' => $data['categoria_id'] ?? null,
            'estado' => $data['estado'] ?? 'Disponible', // El controlador debe enviar el estado. Aquí un fallback.
            'notas_donacion' => $data['notas_donacion'] ?? null, // El controlador debe enviar las notas. Aquí un fallback.
        ];

        if ($currentId) {
            // MODO UPDATE
            $params['id'] = $currentId;
            $sql = "UPDATE {$this->tableName} SET
                        nombre_equipo = :nombre_equipo, marca = :marca, modelo = :modelo, serie = :serie,
                        costo = :costo, fecha_ingreso = :fecha_ingreso,
                        tiempo_depreciacion_anios = :tiempo_depreciacion_anios,
                        categoria_id = :categoria_id, estado = :estado, notas_donacion = :notas_donacion
                    WHERE id = :id";
        } else {
            // MODO CREATE
            $sql = "INSERT INTO {$this->tableName} (nombre_equipo, marca, modelo, serie, costo, fecha_ingreso, tiempo_depreciacion_anios, categoria_id, estado, notas_donacion)
                    VALUES (:nombre_equipo, :marca, :modelo, :serie, :costo, :fecha_ingreso, :tiempo_depreciacion_anios, :categoria_id, :estado, :notas_donacion)";
        }

        Database::getInstance()->query($sql, $params);

        if (!$currentId) {
            return Database::getInstance()->lastInsertId();
        }
        return $currentId;
    }


    /**
     * Busca el último número de serie numérico para un prefijo dado.
     * Utilizado para sugerir el siguiente número en la entrada por lote.
     * Intenta extraer la parte numérica al final del string.
     * Si no se encuentra un prefijo o no hay series que coincidan, busca el máximo global numérico.
     *
     * @param string $prefix Prefijo de serie (ej. "LAPTOP-").
     * @return int El número más alto encontrado, o 0 si no hay coincidencias con el prefijo o globalmente.
     */
    public function findLastSerialNumber($prefix = '')
    {
        // Se utiliza Database::getInstance() directamente para la consulta
        $sqlPrefix = "SELECT serie FROM " . $this->tableName . " WHERE serie LIKE :prefix ORDER BY serie DESC LIMIT 1";
        $resultPrefix = Database::getInstance()->query($sqlPrefix, ['prefix' => $prefix . '%'])->find();
        $lastSerialWithPrefix = $resultPrefix['serie'] ?? null;
        $lastNumericValue = 0;

        if ($lastSerialWithPrefix) {
            if (preg_match('/(\d+)$/', $lastSerialWithPrefix, $matches)) {
                $lastNumericValue = (int)$matches[1];
            }
        }

        // Si no se encontró un número válido con el prefijo, o si el prefijo está vacío,
        // intentamos encontrar el máximo global de series puramente numéricas.
        if (empty($prefix) || $lastNumericValue === 0) {
            $sqlGlobalMax = "SELECT serie FROM " . $this->tableName . " WHERE serie REGEXP '^[0-9]+$' ORDER BY CAST(serie AS UNSIGNED) DESC LIMIT 1";
            $resultGlobalMax = Database::getInstance()->query($sqlGlobalMax)->find();
            $lastGlobalSerial = $resultGlobalMax['serie'] ?? null;
            if ($lastGlobalSerial) {
                $lastNumericValue = max($lastNumericValue, (int)$lastGlobalSerial);
            }
        }

        return $lastNumericValue;
    }


    /**
     * Valida un rango de series para el formulario de lote.
     * Verifica si alguna de las series generadas ya existe en la BD.
     *
     * @param string $prefix Prefijo de serie.
     * @param int $startNumber Número inicial de serie.
     * @param int $count Cantidad de equipos.
     * @return array Un array de series que ya existen en el rango propuesto.
     */
    public function checkBatchSerialRangeForDuplicates(string $prefix, int $startNumber, int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        $seriesToCheck = [];
        for ($i = 0; $i < $count; $i++) {
            // Usamos %04d para 4 dígitos
            $seriesToCheck[] = $prefix . sprintf('%04d', $startNumber + $i);
        }

        // Crear placeholders para la consulta IN
        $placeholders = implode(',', array_fill(0, count($seriesToCheck), '?'));
        $sql = "SELECT serie FROM " . $this->tableName . " WHERE serie IN (" . $placeholders . ")";

        // Ejecutar la consulta usando Database::getInstance()->query()
        return Database::getInstance()->query($sql, $seriesToCheck)->get();
    }

    /**
     * Obtiene un resumen del inventario agrupado por categoría.
     * Calcula el total de equipos, cuántos están asignados y cuántos disponibles.
     *
     * @return array Un array con los datos del resumen.
     */
    public function getSummaryByCategory(): array
    {
        // Se añade la cláusula WHERE para excluir los equipos que ya no están activos.
        $sql = "SELECT 
                    c.nombre AS categoria,
                    COUNT(i.id) AS total_equipos,
                    SUM(CASE WHEN i.estado = 'Asignado' THEN 1 ELSE 0 END) AS equipos_asignados
                FROM categorias c
                LEFT JOIN inventario i ON c.id = i.categoria_id
                WHERE i.estado NOT IN ('Donado', 'En Descarte') OR i.id IS NULL
                GROUP BY c.nombre
                ORDER BY c.nombre ASC";

        return Database::getInstance()->query($sql)->get();
    }
}
