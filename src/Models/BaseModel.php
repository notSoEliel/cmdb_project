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

    /**
     * Obtiene todos los registros con la lógica de ordenamiento corregida.
     */
    public function findAll(array $options = []): array
    {
        $prefix = $this->tableAlias ?? $this->tableName;

        // --- Lógica de Ordenamiento Corregida ---
        $sortColumn = "{$prefix}.id"; // Valor por defecto con prefijo
        // Comprueba si el 'sort' solicitado está en la lista de columnas permitidas.
        if (!empty($options['sort']) && in_array($options['sort'], $this->allowedSortColumns)) {
            $sortColumn = $options['sort'];
        }
        $sortOrder = (!empty($options['order']) && in_array(strtoupper($options['order']), ['ASC', 'DESC'])) ? strtoupper($options['order']) : 'ASC';

        // --- Lógica de Paginación ---
        $perPage = $options['perPage'] ?? 10;
        $page = $options['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT {$prefix}.* FROM {$this->tableName} as {$prefix}";

        list($whereClause, $params) = $this->buildWhereClause($options);
        $sql .= $whereClause;

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

        // --- Lógica de Filtros Específicos ---
        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $column => $value) {
                // Se crea un nombre de placeholder seguro, reemplazando '.' por '_'
                $placeholderName = str_replace('.', '_', $column);
                $placeholder = ":filter_{$placeholderName}";

                // --- INICIO DE LA CORRECCIÓN ---
                // Si la columna ya contiene un punto (ej: 'a.colaborador_id'), se usa tal cual.
                if (strpos($column, '.') !== false) {
                    $finalColumn = $column;
                } else {
                    // Si no, se le añade el prefijo por defecto (ej: 'i.categoria_id').
                    $finalColumn = "{$prefix}.{$column}";
                }
                // --- FIN DE LA CORRECCIÓN ---

                $whereConditions[] = "{$finalColumn} = {$placeholder}";
                $params[$placeholder] = $value;
            }
        }

        // --- Lógica de Búsqueda de Texto Libre ---
        if (!empty($options['search']) && !empty($this->searchableColumns)) {
            $searchTerm = '%' . $options['search'] . '%';
            $searchConditions = [];

            foreach ($this->searchableColumns as $index => $column) {
                $placeholder = ":searchTerm{$index}";
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
