<?php

require_once __DIR__ . '/../../core/model.php';

/**
 * Gestiona los horarios de disponibilidad de los psicólogos.
 */
class Availability extends Model
{
    /**
     * Obtiene los horarios activos de un psicólogo.
     */
    public function getByPsychologistId(int $psychologistId): array
    {
        $sql = "
            SELECT
                id_disponibilidad,
                id_psi,
                dia_semana,
                hora_inicio,
                hora_fin,
                estado
            FROM disponibilidad
            WHERE id_psi = :id_psi
              AND estado = 1
            ORDER BY dia_semana ASC, hora_inicio ASC
        ";

        $stmt = $this->database->prepare($sql);

        $stmt->execute([
            ':id_psi' => $psychologistId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Crea un nuevo horario de disponibilidad.
     */
    public function create(
        int $psychologistId,
        int $dayOfWeek,
        string $startTime,
        string $endTime
    ): int {
        $sql = "
            INSERT INTO disponibilidad (
                id_psi,
                dia_semana,
                hora_inicio,
                hora_fin,
                estado
            ) VALUES (
                :id_psi,
                :dia_semana,
                :hora_inicio,
                :hora_fin,
                1
            )
        ";

        $stmt = $this->database->prepare($sql);

        $stmt->execute([
            ':id_psi' => $psychologistId,
            ':dia_semana' => $dayOfWeek,
            ':hora_inicio' => $startTime,
            ':hora_fin' => $endTime,
        ]);

        return (int) $this->database->lastInsertId();
    }
}