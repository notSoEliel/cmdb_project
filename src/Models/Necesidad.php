<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Necesidad
 * Gestiona las solicitudes de equipos hechas por los colaboradores.
 */
class Necesidad extends BaseModel
{
    protected $tableName = 'necesidades';
    protected $tableAlias = 'n';

    /**
     * Se define la cláusula SELECT y los JOINs por defecto para este modelo,
     * ya que siempre querremos ver el nombre del colaborador que hizo la solicitud.
     */
    public function __construct()
    {
        parent::__construct();
        $this->selectClause = "n.*, CONCAT(co.nombre, ' ', co.apellido) as nombre_colaborador";
        $this->joins = "LEFT JOIN colaboradores co ON n.colaborador_id = co.id";
    }

    // Columnas permitidas para ordenar en la vista de administrador
    protected $allowedSortColumns = ['n.id', 'nombre_colaborador', 'n.estado', 'n.fecha_solicitud'];

    // Columnas donde se podrá buscar
    protected $searchableColumns = ['n.descripcion', 'co.nombre', 'co.apellido'];

    /**
     * Guarda una nueva solicitud de necesidad en la base de datos.
     *
     * @param array $data Debe contener 'colaborador_id' y 'descripcion'.
     * @return bool
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->tableName} (colaborador_id, descripcion, estado)
                VALUES (:colaborador_id, :descripcion, 'Solicitado')";

        $params = [
            'colaborador_id' => $data['colaborador_id'],
            'descripcion' => $data['descripcion']
        ];

        Database::getInstance()->query($sql, $params);
        return true;
    }

    /**
     * Actualiza el estado de una solicitud.
     *
     * @param integer $id El ID de la necesidad.
     * @param string $newState El nuevo estado.
     * @return boolean
     */
    public function updateStatus(int $id, string $newState): bool
    {
        $sql = "UPDATE {$this->tableName} SET estado = :estado WHERE id = :id";
        Database::getInstance()->query($sql, ['estado' => $newState, 'id' => $id]);
        return true;
    }
}
