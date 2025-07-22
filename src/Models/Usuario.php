<?php

namespace App\Models;

use App\Core\Database;

class Usuario extends BaseModel
{
    protected $tableName = 'usuarios';
    protected $tableAlias = 'u';
    protected $allowedSortColumns = ['u.id', 'u.nombre', 'u.email', 'u.activo'];
    protected $searchableColumns = ['nombre', 'email'];

    /**
     * Busca un usuario por su email.
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        return Database::getInstance()->query("SELECT * FROM {$this->tableName} WHERE email = :email", ['email' => $email])->find();
    }

    /**
     * Guarda (Crea o Actualiza) un registro de administrador.
     * @param array $data
     * @return bool
     */
    public function save($data): bool
    {
        $currentId = !empty($data['id']) ? (int)$data['id'] : null;

        // Validar que el email no esté duplicado
        if ($this->exists('email', $data['email'], $currentId)) {
            throw new \Exception("El email '{$data['email']}' ya está en uso.");
        }

        $params = [
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'activo' => $data['activo'] ?? 1, // Por defecto está activo
        ];

        // Solo hashear y guardar la contraseña si se proporciona una nueva
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
                throw new \Exception("La contraseña es obligatoria al crear un nuevo usuario.");
            }
            $columns = implode(', ', array_keys($params));
            $placeholders = ':' . implode(', :', array_keys($params));
            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        }

        Database::getInstance()->query($sql, $params);
        return true;
    }

    /**
     * Actualiza únicamente la contraseña de un administrador.
     * @param integer $id
     * @param string $newPasswordHash
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
}
