<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{

    // Añade esta nueva propiedad al principio de la clase BaseModel
    protected $tableAlias = null;

    protected $tableName;
    protected $allowedSortColumns = ['id'];
    protected $searchableColumns = [];

    // Nuevas propiedades para que los modelos hijos personalicen la consulta
    protected $selectClause = '';
    protected $joins = '';
    protected $groupBy = '';

    public function __construct()
    {
        // Si no se define una cláusula SELECT, se usa la por defecto
        if (empty($this->selectClause)) {
            $this->selectClause = "{$this->tableName}.*";
        }
    }

    public function countFiltered(array $options = []): int
    {
        $sql = "SELECT COUNT(DISTINCT {$this->tableName}.id) as total FROM {$this->tableName}";
        $sql .= " " . $this->joins; // Añadir los joins
        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        $result = Database::getInstance()->query($sql, $params)->find();
        return $result['total'] ?? 0;
    }

    public function findAll(array $options = []): array
    {
        $sortColumn = "{$this->tableName}.id";
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = "{$this->tableName}." . $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';

        $perPage = $options['perPage'] ?? 10;
        $page = $options['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT {$this->selectClause} FROM {$this->tableName}";
        $sql .= " " . $this->joins; // Añadir los joins

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        $sql .= " ORDER BY {$sortColumn} {$sortOrder} LIMIT {$perPage} OFFSET {$offset}";

        return Database::getInstance()->query($sql, $params)->get();
    }



    /**
     * Construye la cláusula WHERE y los parámetros para la consulta.
     * @param array $options
     * @return array [string $whereClause, array $params]
     */
    protected function buildWhereClause(array $options = []): array
    {
        // Usa el alias si está definido, si no, usa el nombre de la tabla.
        $prefix = $this->tableAlias ?? $this->tableName;

        $whereConditions = [];
        $params = [];

        // Filtros específicos
        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                $placeholder = ":filter_{$column}";
                // AHORA USA EL PREFIJO CORRECTO (ej: 'i.categoria_id')
                $whereConditions[] = "{$prefix}.{$column} = {$placeholder}";
                $params[$placeholder] = $value;
            }
        }

        // Búsqueda de texto libre
        if (!empty($options['search']) && !empty($this->searchableColumns)) {
            $searchTerm = '%' . $options['search'] . '%';
            $searchConditions = [];

            foreach ($this->searchableColumns as $index => $column) {
                $placeholder = ":searchTerm{$index}";
                // AHORA USA EL PREFIJO CORRECTO (ej: 'i.nombre_equipo')
                $searchConditions[] = "{$prefix}.{$column} LIKE {$placeholder}";
                $params[$placeholder] = $searchTerm;
            }

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
