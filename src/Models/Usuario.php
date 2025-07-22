<?php

namespace App\Models;

use App\Core\Database;

class Usuario extends BaseModel
{
    protected $tableName = 'usuarios';
    protected $tableAlias = 'u';

    public function findByEmail(string $email)
    {
        return Database::getInstance()->query("SELECT * FROM {$this->tableName} WHERE email = :email", ['email' => $email])->find();
    }
}
