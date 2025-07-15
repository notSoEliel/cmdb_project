<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Colaborador
 * Gestiona los datos de la tabla 'colaboradores' y hereda la lógica
 * de búsqueda, paginación y ordenamiento del BaseModel.
 */
class Colaborador extends BaseModel
{
    /**
     * @var string El nombre de la tabla en la base de datos.
     */
    protected $tableName = 'colaboradores';
    // Le damos un alias a la tabla
    protected $tableAlias = 'co';
    /**
     * @var array Columnas permitidas para el ordenamiento dinámico.
     */
    protected $allowedSortColumns = [
        'co.id',
        'co.nombre',
        'co.apellido',
        'co.identificacion_unica',
        'co.email'
    ];
    /**
     * @var array Columnas en las que se realizará la búsqueda de texto libre.
     */
    protected $searchableColumns = ['nombre', 'apellido', 'identificacion_unica', 'email', 'ubicacion', 'telefono'];

    /**
     * Guarda (Crea o Actualiza) un registro de colaborador.
     * Este método es específico de este modelo debido a su lógica particular
     * para manejar la contraseña.
     *
     * @param array $data Datos del colaborador.
     * @return bool
     */
    public function save($data)
    {
        $params = [
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'identificacion_unica' => $data['identificacion_unica'],
            'email' => $data['email'],
            'ubicacion' => $data['ubicacion'],
            'telefono' => $data['telefono'],
        ];

        // Hashear la contraseña solo si se proporciona una nueva al editar.
        if (!empty($data['password'])) {
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['id']) && !empty($data['id'])) {
            // --- Actualizar un colaborador existente ---
            $params['id'] = $data['id'];
            $setClauses = [];
            foreach ($params as $key => $value) {
                if ($key !== 'id') {
                    $setClauses[] = "$key = :$key";
                }
            }
            $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setClauses) . " WHERE id = :id";
        } else {
            // --- Crear un nuevo colaborador ---
            if (empty($params['password_hash'])) {
                die("El campo de contraseña es obligatorio al crear un nuevo colaborador.");
            }
            $columns = implode(', ', array_keys($params));
            $placeholders = ':' . implode(', :', array_keys($params));
            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }
}
