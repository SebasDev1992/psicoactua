<?php

require_once __DIR__ . '/../../core/database.php';

/**
 * Consulta los datos de perfil que relacionan a un usuario con un paciente.
 */
class Patient
{
    /**
     * Busca la información del paciente asociada a un usuario.
     */
    public static function findByUserId(int $userId): ?array
    {
        $database = new Database();
        $db = $database->connection();

        // El JOIN reúne datos de cuenta y datos personales en una sola respuesta.
        $sql = "
            SELECT
                u.id_user,
                u.nom,
                u.ape,
                u.email,
                u.tel,
                p.id_pac,
                p.fecha_nac,
                p.genero,
                p.direccion
            FROM users u
            INNER JOIN pacientes p
                ON p.id_user = u.id_user
            WHERE u.id_user = :id_user
            LIMIT 1
        ";

        // El identificador se enlaza como parámetro para no alterar el SQL.
        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':id_user' => $userId
        ]);

        $patient = $stmt->fetch();

        return $patient ?: null;
    }
}
