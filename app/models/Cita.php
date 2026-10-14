<?php

require_once __DIR__ . '/../../core/model.php';

/**
 * Modelo encargado de gestionar las citas.
 *
 * Centraliza las consultas relacionadas con:
 * - Citas ocupadas de un psicólogo.
 * - Validación de disponibilidad.
 * - Validación de citas simultáneas del paciente.
 * - Creación de nuevas citas.
 */
class Cita extends Model
{
    /**
     * Obtiene las horas ocupadas de un psicólogo para una fecha.
     *
     * Las citas canceladas no bloquean el horario.
     */
    public function getOccupiedTimes(
        int $psychologistId,
        string $date
    ): array {
        $sql = "
            SELECT hora
            FROM citas
            WHERE id_psi = :id_psi
              AND fecha = :fecha
              AND estado <> 'cancelada'
            ORDER BY hora ASC
        ";

        $stmt = $this->database->prepare($sql);
        $stmt->execute([
            ':id_psi' => $psychologistId,
            ':fecha' => $date,
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Comprueba si una hora concreta ya está ocupada.
     *
     * Se utiliza nuevamente en el servidor justo antes de crear
     * la cita para evitar reservas duplicadas.
     */
    public function isTimeOccupied(
        int $psychologistId,
        string $date,
        string $time
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM citas
            WHERE id_psi = :id_psi
              AND fecha = :fecha
              AND hora = :hora
              AND estado <> 'cancelada'
        ";
        //statement(sentencia) para preparar la consulta y luego ejecutarla con los parámetros proporcionados
        $stmt = $this->database->prepare($sql);
        $stmt->execute([
            ':id_psi' => $psychologistId,
            ':fecha' => $date,
            ':hora' => $time,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Comprueba si el paciente ya tiene una cita activa
     * en la misma fecha y hora.
     */
    public function patientHasAppointment(
        int $patientId,
        string $date,
        string $time
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM citas
            WHERE id_pac = :id_pac
              AND fecha = :fecha
              AND hora = :hora
              AND estado <> 'cancelada'
        ";

        $stmt = $this->database->prepare($sql);
        $stmt->execute([
            ':id_pac' => $patientId,
            ':fecha' => $date,
            ':hora' => $time,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Crea una nueva cita con estado pendiente.
     */
    public function create(
        int $patientId,
        int $psychologistId,
        string $date,
        string $time
    ): int {
        $sql = "
            INSERT INTO citas (
                id_pac,
                id_psi,
                fecha,
                hora,
                estado
            ) VALUES (
                :id_pac,
                :id_psi,
                :fecha,
                :hora,
                'pendiente'
            )
        ";

        $stmt = $this->database->prepare($sql);
        $stmt->execute([
            ':id_pac' => $patientId,
            ':id_psi' => $psychologistId,
            ':fecha' => $date,
            ':hora' => $time,
        ]);

        return (int) $this->database->lastInsertId();
    }
}