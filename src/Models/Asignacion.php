<?php

namespace App\Models;

use App\Core\Database;

/**
 * Modelo Asignacion
 * Gestiona las operaciones de la tabla 'asignaciones' y su interacción
 * con la tabla 'inventario' mediante transacciones.
 */
class Asignacion
{
    /**
     * Crea un nuevo registro de asignación y actualiza el estado del equipo.
     *
     * @param int $inventario_id El ID del equipo a asignar.
     * @param int $colaborador_id El ID del colaborador que recibe el equipo.
     * @return bool True si la transacción fue exitosa.
     * @throws \Exception Si ocurre un error durante la transacción.
     */
    public function create(int $inventario_id, int $colaborador_id): bool
    {
        $db = Database::getInstance();

        try {
            // Inicia una transacción
            $db->beginTransaction();

            // 1. Insertar el nuevo registro en la tabla de asignaciones
            $sql_insert = "INSERT INTO asignaciones (inventario_id, colaborador_id, fecha_asignacion) VALUES (:inventario_id, :colaborador_id, CURDATE())";
            $db->query($sql_insert, [
                'inventario_id' => $inventario_id,
                'colaborador_id' => $colaborador_id
            ]);

            // 2. Actualizar el estado del equipo en la tabla de inventario
            $sql_update = "UPDATE inventario SET estado = 'Asignado' WHERE id = :inventario_id";
            $db->query($sql_update, ['inventario_id' => $inventario_id]);

            // Si ambas consultas fueron exitosas, confirma la transacción
            $db->commit();

            return true;
        } catch (\Exception $e) {
            // Si algo falla, revierte todos los cambios
            $db->rollBack();
            // Lanza la excepción para que el controlador pueda manejarla
            throw $e;
        }
    }

    // Aquí añadiremos más adelante el método para des-asignar un equipo.
}
