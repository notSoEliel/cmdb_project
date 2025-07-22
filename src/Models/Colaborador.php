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
    protected $tableName = 'colaboradores';
    protected $tableAlias = 'co';

    protected $allowedSortColumns = [
        'co.id',
        'co.nombre',
        'co.apellido',
        'co.identificacion_unica',
        'co.email'
    ];

    protected $searchableColumns = ['nombre', 'apellido', 'identificacion_unica', 'email'];

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
        // Se obtiene el ID actual (si estamos editando) para excluirlo de la comprobación.
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        // --- INICIO DE VALIDACIONES PREVIAS ---
        // Se usa nuestro nuevo método reutilizable para verificar duplicados.
        if ($this->exists('email', $data['email'], $currentId)) {
            // Se lanza una excepción con un mensaje claro.
            throw new \Exception("El email '{$data['email']}' ya está en uso por otro colaborador.");
        }
        if ($this->exists('identificacion_unica', $data['identificacion_unica'], $currentId)) {
            throw new \Exception("La identificación '{$data['identificacion_unica']}' ya está registrada.");
        }
        // --- FIN DE VALIDACIONES PREVIAS ---

        // Si pasa las validaciones, se procede con la lógica de guardado.
        $params = [
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'departamento' => $data['departamento'] ?? null,
            'identificacion_unica' => $data['identificacion_unica'],
            'email' => $data['email'],
            'ubicacion' => $data['ubicacion'],
            'telefono' => $data['telefono'],
            'ip_asignada' => $data['ip_asignada'] ?? null,
        ];

        // --- LÓGICA PARA SUBIR LA IMAGEN ---
        if (isset($files['foto_perfil']) && $files['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../public/uploads/colaboradores/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = uniqid() . '-' . basename($files['foto_perfil']['name']);
            if (move_uploaded_file($files['foto_perfil']['tmp_name'], $uploadDir . $fileName)) {
                $params['foto_perfil'] = $fileName; // Añade el nombre del archivo a los parámetros
            }
        }


        if (!empty($data['password'])) {
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($currentId) {
            // Actualizar
            $params['id'] = $currentId;
            $setClauses = [];
            foreach ($params as $key => $value) {
                if ($key !== 'id') $setClauses[] = "$key = :$key";
            }
            $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setClauses) . " WHERE id = :id";
        } else {
            // Crear
            if (empty($params['password_hash'])) {
                throw new \Exception("La contraseña es obligatoria al crear un nuevo colaborador.");
            }
            $columns = implode(', ', array_keys($params));
            $placeholders = ':' . implode(', :', array_keys($params));
            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }

    /**
     * Actualiza únicamente la ubicación de un colaborador específico.
     *
     * @param integer $id El ID del colaborador.
     * @param string $location La nueva ubicación.
     * @return boolean
     */
    public function updateLocation(int $id, string $location): bool
    {
        $sql = "UPDATE {$this->tableName} SET ubicacion = :ubicacion WHERE id = :id";
        Database::getInstance()->query($sql, [
            'ubicacion' => $location,
            'id' => $id
        ]);
        return true;
    }

    /**
     * Actualiza únicamente la contraseña de un colaborador.
     *
     * @param integer $id El ID del colaborador.
     * @param string $newPasswordHash La nueva contraseña ya hasheada.
     * @return boolean
     */
    public function updatePassword(int $id, string $newPasswordHash): bool
    {
        $sql = "UPDATE {$this->tableName} SET password_hash = :password_hash WHERE id = :id";
        Database::getInstance()->query($sql, [
            'password_hash' => $newPasswordHash,
            'id' => $id
        ]);
        return true;
    }

    /**
     * Busca un colaborador por su email.
     *
     * @param string $email El email del colaborador.
     * @return array|null
     */
    public function findByEmail(string $email)
    {
        return Database::getInstance()->query("SELECT * FROM {$this->tableName} WHERE email = :email", ['email' => $email])->find();
    }
}
