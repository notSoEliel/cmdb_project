<?php

namespace App\Models;

use App\Core\Database;


/**
 * Modelo Colaborador
 * Gestiona los datos de la tabla 'colaboradores'.
 */
class Colaborador extends BaseModel
{
    /**
     * @var string El nombre de la tabla.
     */
    protected $tableName = 'colaboradores';

    /**
     * @var array Columnas permitidas para el ordenamiento.
     */
    protected $allowedSortColumns = ['id', 'nombre', 'apellido', 'identificacion_unica', 'email'];

    /**
     * Guarda un registro de colaborador.
     * Maneja la creación y actualización, incluyendo el hasheo de la contraseña.
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

        // Hashear la contraseña solo si se proporciona una nueva.
        // Esto evita sobreescribir la contraseña existente si el campo se deja vacío al editar.
        if (!empty($data['password'])) {
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['id']) && !empty($data['id'])) {
            // --- Actualizar un colaborador existente ---
            $params['id'] = $data['id'];

            // Construir la parte SET de la consulta dinámicamente
            $setClauses = [];
            foreach ($params as $key => $value) {
                if ($key !== 'id') {
                    $setClauses[] = "$key = :$key";
                }
            }
            $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setClauses) . " WHERE id = :id";
        } else {
            // --- Crear un nuevo colaborador ---
            // Se requiere contraseña al crear un nuevo colaborador
            if (empty($params['password_hash'])) {
                // Podríamos lanzar una excepción o retornar un error.
                // Por simplicidad, aquí detenemos la ejecución con un mensaje.
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
