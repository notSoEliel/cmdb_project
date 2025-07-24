<?php

namespace App\Models;

use App\Core\Database;

/**
 * Clase Abstracta BaseModel
 *
 * Esta es la clase "cerebro" de la que heredan todos nuestros modelos.
 * Contiene toda la lógica reutilizable para buscar, filtrar, ordenar, paginar,
 * contar, y verificar la existencia de registros. Los modelos hijos
 * solo necesitan configurar sus propiedades para funcionar.
 */
abstract class BaseModel
{
    // --- PROPIEDADES DE CONFIGURACIÓN (para los modelos hijos) ---

    /** @var string El nombre exacto de la tabla en la base de datos. */
    protected $tableName;
    /** @var string Un alias corto para la tabla, usado en consultas complejas con JOINs. */
    protected $tableAlias;
    /** @var array Lista de columnas por las que se permite ordenar (deben incluir el alias, ej: 'i.nombre'). */
    protected $allowedSortColumns = [];
    /** @var array Lista de columnas en las que se puede buscar (pueden incluir alias, ej: 'co.nombre'). */
    protected $searchableColumns = [];
    /** @var string La cláusula SELECT personalizada para la consulta (ej: para añadir campos calculados). */
    protected $selectClause = '';
    /** @var string La cláusula JOIN completa para unir con otras tablas. */
    protected $joins = '';
    /** @var string La cláusula GROUP BY, si es necesaria. */
    protected $groupBy = '';


    /**
     * El constructor se asegura de que siempre haya una cláusula SELECT.
     */
    public function __construct()
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        if (empty($this->selectClause)) {
            $this->selectClause = "{$prefix}.*";
        }
    }

    /**
     * Cuenta todos los registros que coinciden con los filtros y la búsqueda.
     * @param array $options Opciones de filtro y búsqueda.
     * @return int El número total de registros.
     */
    public function countFiltered(array $options = []): int
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $sql = "SELECT COUNT(DISTINCT {$prefix}.id) as total 
                FROM {$this->tableName} AS {$prefix}
                {$this->joins}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result['total'] ?? 0;
    }

    /**
     * Obtiene una lista paginada de registros con filtros, búsqueda y orden.
     * @param array $options Opciones completas.
     * @return array La lista de registros.
     */
    public function findAll(array $options = []): array
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $sortColumn = "{$prefix}.id";
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';
        $perPage = (int)($options['perPage'] ?? 10);
        $page = (int)($options['page'] ?? 1);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT {$this->selectClause} 
                FROM {$this->tableName} AS {$prefix} 
                {$this->joins}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        $sql .= " ORDER BY {$sortColumn} {$sortOrder}";

        if (($options['paginate'] ?? true) !== false) {
            $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        }

        return Database::getInstance()->query($sql, $params)->get();
    }

    /**
     * Construye la cláusula WHERE de forma segura y dinámica.
     * @param array $options Opciones de búsqueda y filtro.
     * @return array Contiene la cadena SQL del WHERE y los parámetros.
     */
    protected function buildWhereClause(array $options = []): array
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $whereConditions = [];
        $params = [];

        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                $safeColumnName = str_replace('.', '_', $column);
                $finalColumn = (strpos($column, '.') !== false) ? $column : "{$prefix}.{$column}";

                // Lógica simplificada: si el valor es un array, es IN o NOT IN.
                if (is_array($value)) {
                    $operator = array_shift($value); // Toma el 'NOT IN'
                    $placeholders = [];
                    foreach ($value as $idx => $v) {
                        $placeholder = ":filter_{$safeColumnName}_{$idx}";
                        $placeholders[] = $placeholder;
                        $params[$placeholder] = $v;
                    }
                    if (!empty($placeholders)) {
                        $whereConditions[] = "{$finalColumn} {$operator} (" . implode(',', $placeholders) . ")";
                    }
                } else {
                    // Se crea un nombre de placeholder limpio.
                    $placeholder = ":filter_" . $safeColumnName;
                    // Se asegura de que solo haya un ':' en la consulta final.
                    $whereConditions[] = "{$finalColumn} = {$placeholder}";
                    $params[$placeholder] = $value;
                }
            }
        }

        if (!empty($options['search']) && !empty($this->searchableColumns)) {
            $searchTerm = '%' . $options['search'] . '%';
            $searchConditions = [];
            foreach ($this->searchableColumns as $index => $column) {
                $placeholder = ":searchTerm{$index}";
                $finalColumn = (strpos($column, '.') !== false) ? $column : "{$prefix}.{$column}";
                $searchConditions[] = "{$finalColumn} LIKE {$placeholder}";
                $params[$placeholder] = $searchTerm;
            }
            if (!empty($searchConditions)) {
                $whereConditions[] = "(" . implode(' OR ', $searchConditions) . ")";
            }
        }
        return [!empty($whereConditions) ? " WHERE " . implode(' AND ', $whereConditions) : "", $params];
    }

    /**
     * Busca un único registro por su ID.
     * @param int $id El ID del registro a buscar.
     * @return mixed El registro encontrado o false.
     */
    public function findById($id)
    {
        $prefix = $this->tableAlias ?? $this->tableName;
        $sql = "SELECT {$this->selectClause} 
                FROM {$this->tableName} AS {$prefix}
                {$this->joins} 
                WHERE {$prefix}.id = :id";
        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }
        return Database::getInstance()->query($sql, ['id' => $id])->find();
    }

    /**
     * Elimina un registro por su ID.
     * @param int $id El ID del registro a eliminar.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        Database::getInstance()->query($sql, ['id' => $id]);
    }

    /**
     * Verifica si un valor ya existe en una columna.
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
        return Database::getInstance()->query($sql, $params)->find() !== false;
    }
}
