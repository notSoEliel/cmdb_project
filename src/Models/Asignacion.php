<?php

namespace App\Models;

use App\Core\Database; // Asegúrate de que esta línea esté presente

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

    public function unassign(int $asignacion_id, int $inventario_id): bool
    {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            // 1. Marca la asignación como devuelta
            $db->query("UPDATE asignaciones SET fecha_devolucion = CURDATE() WHERE id = :asignacion_id", ['asignacion_id' => $asignacion_id]);
            // 2. Actualiza el estado del equipo
            $db->query("UPDATE inventario SET estado = 'En Stock' WHERE id = :inventario_id", ['inventario_id' => $inventario_id]);
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la asignación activa de un equipo y le pone fecha de devolución.
     * @param int $inventario_id
     * @return bool
     */
    public function unassignByInventarioId(int $inventario_id): bool
    {
        $sql = "UPDATE asignaciones 
                SET fecha_devolucion = CURDATE() 
                WHERE inventario_id = :inventario_id AND fecha_devolucion IS NULL";

        Database::getInstance()->query($sql, ['inventario_id' => $inventario_id]);
        return true;
    }

    /**
     * Verifica si un equipo está actualmente asignado a un colaborador específico.
     *
     * @param int $inventario_id El ID del equipo a verificar.
     * @param int $colaborador_id El ID del colaborador.
     * @return bool True si el equipo está asignado a ese colaborador y la asignación está activa, False en caso contrario.
     */
    public function isEquipoAssignedToColaborador(int $inventario_id, int $colaborador_id): bool
    {
        $sql = "SELECT COUNT(*) FROM asignaciones
                WHERE inventario_id = :inventario_id
                AND colaborador_id = :colaborador_id
                AND fecha_devolucion IS NULL"; // Solo asignaciones activas

        $result = Database::getInstance()->query($sql, [
            'inventario_id' => $inventario_id,
            'colaborador_id' => $colaborador_id
        ])->find(); // Usamos find() y no get() porque esperamos solo un count

        return $result['COUNT(*)'] > 0;
    }
}
