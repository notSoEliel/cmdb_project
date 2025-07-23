<?php
namespace App\Models;

use App\Core\Database;

class PasswordReset
{
    /**
     * Guarda un nuevo token de reseteo en la base de datos.
     * @param string $email
     * @param string $token
     * @param string $expiresAt Fecha de expiración en formato Y-m-d H:i:s
     * @return void
     */
    public function saveToken(string $email, string $token, string $expiresAt): void
    {
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
        Database::getInstance()->query($sql, [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Busca un registro de reseteo por su token.
     * @param string $token
     * @return mixed El registro si se encuentra, o false.
     */
    public function findByToken(string $token)
    {
        $sql = "SELECT * FROM password_resets WHERE token = :token";
        return Database::getInstance()->query($sql, ['token' => $token])->find();
    }

    /**
     * Elimina un token de la base de datos.
     * @param string $token
     * @return void
     */
    public function deleteToken(string $token): void
    {
        $sql = "DELETE FROM password_resets WHERE token = :token";
        Database::getInstance()->query($sql, ['token' => $token]);
    }
}