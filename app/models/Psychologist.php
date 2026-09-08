<?php

require_once __DIR__ . '/../../core/model.php';

/**
 * Modelo encargado de consultar la información de los psicólogos.
 */
class Psychologist extends Model
{
    /**
     * Obtiene los psicólogos activos con sus datos de usuario.
     */
    public function getActivePsychologists(): array
    {
        $sql = "
            SELECT
                p.id_psi,
                p.id_user,
                p.especialidad,
                p.licencia,
                u.nom,
                u.ape
            FROM psicologos p
            INNER JOIN users u
                ON u.id_user = p.id_user
            WHERE u.estado = 1
            ORDER BY u.nom ASC, u.ape ASC
        ";

        $stmt = $this->database->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * Busca el psicólogo asociado a un usuario.
     */
    public function findByUserId(int $userId): ?array
    {
        $sql = "
            SELECT
                p.id_psi,
                p.id_user,
                p.especialidad,
                p.licencia,
                u.nom,
                u.ape
            FROM psicologos p
            INNER JOIN users u
                ON u.id_user = p.id_user
            WHERE p.id_user = :id_user
            LIMIT 1
        ";

        $stmt = $this->database->prepare($sql);

        $stmt->execute([
            ':id_user' => $userId,
        ]);

        $psychologist = $stmt->fetch();

        return $psychologist ?: null;
    }

}