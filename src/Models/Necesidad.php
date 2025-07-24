<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Necesidad
 * Gestiona las solicitudes de equipos hechas por los colaboradores.
 * Ahora incluye lógica para manejar el ciclo de vida de una solicitud.
 */
class Necesidad extends BaseModel
{
    protected $tableName = 'necesidades';
    protected $tableAlias = 'n';

    public function __construct()
    {
        parent::__construct();
        $this->selectClause = "n.*, CONCAT(co.nombre, ' ', co.apellido) as nombre_colaborador";
        $this->joins = "LEFT JOIN colaboradores co ON n.colaborador_id = co.id";
    }

    protected $allowedSortColumns = ['n.id', 'nombre_colaborador', 'n.estado', 'n.fecha_solicitud'];
    protected $searchableColumns = ['n.descripcion', 'co.nombre', 'co.apellido'];

    /**
     * Guarda una solicitud (la crea si es nueva, la actualiza si ya existe).
     * Incluye la lógica para "reabrir" una solicitud rechazada.
     *
     * @param array $data Debe contener 'descripcion' y 'colaborador_id'. Puede contener 'id'.
     * @return bool
     */
    public function save(array $data): bool
    {
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        if ($currentId) {
            // --- MODO EDICIÓN (REABRIR SOLICITUD) ---
            // Si una solicitud rechazada se edita, se vuelve a abrir.
            $sql = "UPDATE {$this->tableName} SET 
                        descripcion = :descripcion, 
                        estado = 'Solicitado', 
                        fecha_solicitud = NOW() 
                    WHERE id = :id AND colaborador_id = :colaborador_id";

            $params = [
                'id' => $currentId,
                'colaborador_id' => $data['colaborador_id'],
                'descripcion' => $data['descripcion']
            ];
        } else {
            // --- MODO CREACIÓN ---
            $sql = "INSERT INTO {$this->tableName} (colaborador_id, descripcion, estado) 
                    VALUES (:colaborador_id, :descripcion, 'Solicitado')";

            $params = [
                'colaborador_id' => $data['colaborador_id'],
                'descripcion' => $data['descripcion']
            ];
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }

    /**
     * Actualiza el estado de una solicitud (acción del administrador).
     * Si el estado es Completado o Rechazado, registra la fecha de resolución.
     *
     * @param integer $id El ID de la necesidad.
     * @param string $newState El nuevo estado.
     * @return boolean
     */
    public function updateStatus(int $id, string $newState): bool
    {
        $fechaResolucion = null;
        // Si el estado es uno de cierre, se establece la fecha de resolución.
        if (in_array($newState, ['Completado', 'Rechazado'])) {
            $fechaResolucion = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE {$this->tableName} SET 
                    estado = :estado, 
                    fecha_resolucion = :fecha_resolucion 
                WHERE id = :id";

        Database::getInstance()->query($sql, [
            'estado' => $newState,
            'fecha_resolucion' => $fechaResolucion,
            'id' => $id
        ]);
        return true;
    }
}
