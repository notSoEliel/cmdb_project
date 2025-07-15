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
        $sortColumn = "{$this->tableAlias}.id";
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
                FROM {$this->tableName} AS {$this->tableAlias} 
                {$this->joins}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        $sql .= " ORDER BY {$sortColumn} {$sortOrder} LIMIT {$perPage} OFFSET {$offset}";

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
                $placeholderName = str_replace('.', '_', $column);
                $placeholder = ":filter_{$placeholderName}";
                $finalColumn = (strpos($column, '.') !== false) ? $column : "{$prefix}.{$column}";
                $whereConditions[] = "{$finalColumn} = {$placeholder}";
                $params[$placeholder] = $value;
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

    public function findById($id)
    {
        $sql = "SELECT {$this->selectClause} FROM {$this->tableName} {$this->joins} WHERE {$this->tableName}.id = :id";
        return Database::getInstance()->query($sql, ['id' => $id])->find();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        Database::getInstance()->query($sql, ['id' => $id]);
        return true;
    }
}
