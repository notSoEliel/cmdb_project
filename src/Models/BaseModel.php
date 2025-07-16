<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    // Propiedades que los hijos DEBEN definir
    protected $tableName;
    protected $tableAlias;
    protected $allowedSortColumns = [];
    protected $searchableColumns = [];

    // Propiedades que los hijos PUEDEN definir para personalizar la consulta
    protected $selectClause = '';
    protected $joins = '';
    protected $groupBy = '';

    public function __construct()
    {
        // Si el hijo no define un SELECT, se crea uno por defecto
        if (empty($this->selectClause)) {
            $this->selectClause = "{$this->tableAlias}.*";
        }
    }

    /**
     * Cuenta todos los registros que coinciden con los filtros.
     * Este método ahora es 100% genérico.
     */
    public function countFiltered(array $options = []): int
    {
        $sql = "SELECT COUNT(DISTINCT {$this->tableAlias}.id) as total 
                FROM {$this->tableName} AS {$this->tableAlias}
                {$this->joins}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result['total'] ?? 0;
    }

    /**
     * Obtiene todos los registros. Este método ahora es 100% genérico.
     */
    public function findAll(array $options = []): array
    {
        // Lógica de Ordenamiento
        $prefix = $this->tableAlias ?? $this->tableName;
        $sortColumn = "{$prefix}.id";
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';

        // Lógica de Paginación
        $perPage = (int)($options['perPage'] ?? 10);
        $page = (int)($options['page'] ?? 1);
        $offset = ($page - 1) * $perPage;

        // Construcción de la consulta usando las piezas del modelo hijo
        $sql = "SELECT {$this->selectClause} 
                FROM {$this->tableName} AS {$prefix} 
                {$this->joins}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        // --- INICIO DE LA CORRECCIÓN ---
        // Se añade un espacio al principio de las cláusulas para evitar errores de sintaxis.
        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        $sql .= " ORDER BY {$sortColumn} {$sortOrder}";

        // La paginación se aplica solo si no se indica explícitamente lo contrario.
        // Esto es útil para los reportes de Excel.
        if (($options['paginate'] ?? true) !== false) {
            $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        }
        // --- FIN DE LA CORRECCIÓN ---

        return Database::getInstance()->query($sql, $params)->get();
    }



    /**
     * Construye la cláusula WHERE y los parámetros para la consulta de forma segura.
     * Ahora es capaz de manejar nombres de columna con alias (ej: 'a.colaborador_id').
     *
     * @param array $options Opciones de búsqueda y filtro.
     * @return array Un array con la cláusula WHERE y los parámetros.
     */
    protected function buildWhereClause(array $options = []): array
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $whereConditions = [];
        $params = [];

        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                // Se crea un nombre de columna seguro para usar en los placeholders
                $safeColumnName = str_replace('.', '_', $column);
                $finalColumn = (strpos($column, '.') !== false) ? $column : "{$prefix}.{$column}";
                // Si el valor del filtro es un array, creamos una cláusula IN o NOT IN
                if (is_array($value)) {
                    // El primer elemento del array es el operador (IN, NOT IN)
                    $operator = array_shift($value);
                    // Creamos placeholders únicos para cada valor en el array
                    $placeholders = [];
                    foreach ($value as $idx => $v) {
                        // Se usa el nombre de columna seguro para crear un placeholder válido (sin puntos)
                        $placeholder = ":filter_{$safeColumnName}_{$idx}";
                        $placeholders[] = $placeholder;
                        $params[$placeholder] = $v;
                    }
                    $whereConditions[] = "{$finalColumn} {$operator} (" . implode(',', $placeholders) . ")";
                } else {
                    // Si no es un array, usamos el comparador '=' de siempre
                    $placeholder = ":filter_" . str_replace('.', '_', $column);
                    $whereConditions[] = "{$finalColumn} = {$placeholder}";
                    $params[$placeholder] = $value;
                }
            }
        }

        // --- Lógica de Búsqueda de Texto Libre ---
        if (!empty($options['search']) && !empty($this->searchableColumns)) {
            $searchTerm = '%' . $options['search'] . '%';
            $searchConditions = [];

            // --- INICIO DE LA CORRECCIÓN ---
            foreach ($this->searchableColumns as $index => $column) {
                $placeholder = ":searchTerm{$index}";

                // Se aplica la misma lógica que en los filtros:
                // Si la columna ya contiene un punto (ej: 'co.nombre'), se usa tal cual.
                if (strpos($column, '.') !== false) {
                    $finalColumn = $column;
                } else {
                    // Si no, se le añade el prefijo por defecto (ej: 'nombre' se convierte en 'i.nombre').
                    $finalColumn = "{$prefix}.{$column}";
                }

                $searchConditions[] = "{$finalColumn} LIKE {$placeholder}";
                $params[$placeholder] = $searchTerm;
            }
            // --- FIN DE LA CORRECCIÓN ---

            if (!empty($searchConditions)) {
                $whereConditions[] = "(" . implode(' OR ', $searchConditions) . ")";
            }
        }

        return [
            !empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "",
            $params
        ];
    }

    /**
     * Busca un único registro por su ID, usando el alias de la tabla.
     *
     * @param int $id El ID del registro a buscar.
     * @return mixed El registro encontrado o false.
     */
    public function findById($id)
    {
        // Se asegura de usar el alias en el FROM y en el WHERE para consistencia.
        $prefix = $this->tableAlias ?? $this->tableName;

        $sql = "SELECT {$this->selectClause} 
                FROM {$this->tableName} AS {$prefix}
                {$this->joins} 
                WHERE {$prefix}.id = :id";

        // Para consultas complejas con GROUP BY, tomamos solo el primer resultado.
        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        return Database::getInstance()->query($sql, ['id' => $id])->find();
    }

    /**
     * Elimina un registro por su ID.
     *
     * @param int $id El ID del registro a eliminar.
     * @return bool
     */
    public function delete($id)
    {
        // La consulta DELETE es simple y no necesita alias,
        // pero la mantenemos así para claridad.
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        Database::getInstance()->query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Verifica si un valor ya existe en una columna específica.
     * Es capaz de excluir un ID para poder usarse en actualizaciones.
     *
     * @param string $column El nombre de la columna a verificar.
     * @param mixed $value El valor a buscar.
     * @param int|null $excludeId Un ID para excluir de la búsqueda (útil al actualizar).
     * @return bool True si el valor existe, false si no.
     */
    public function exists(string $column, $value, ?int $excludeId = null): bool
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $sql = "SELECT id FROM {$this->tableName} AS {$prefix} WHERE {$prefix}.{$column} = :value";
        $params = [':value' => $value];

        if ($excludeId !== null) {
            $sql .= " AND {$prefix}.id != :excludeId";
            $params[':excludeId'] = $excludeId;
        }

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result !== false;
    }
}
