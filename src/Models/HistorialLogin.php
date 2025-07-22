<?php

namespace App\Models;

use App\Core\Database;

class HistorialLogin
{
    /**
     * Guarda un nuevo registro de inicio de sesión para un colaborador.
     * @param int $colaborador_id
     */
    public function save(int $colaborador_id): void
    {
        $sql = "INSERT INTO historial_login (colaborador_id, ip_origen) VALUES (:colaborador_id, :ip_origen)";
        $params = [
            'colaborador_id' => $colaborador_id,
            'ip_origen' => $_SERVER['REMOTE_ADDR'] ?? 'Desconocida'
        ];
        Database::getInstance()->query($sql, $params);
    }
}
