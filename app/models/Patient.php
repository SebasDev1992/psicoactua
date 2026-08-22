<?php

require_once __DIR__ . '/../../core/model.php';

/**
 * Consulta los datos de perfil que relacionan a un usuario con un paciente.
 */
class Patient extends Model
{
    /**
     * Busca la información del paciente asociada a un usuario.
     */
    public function findByUserId(int $userId): ?array
    {
       
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
      $stmt = $this->database->prepare($sql);;

        $stmt->execute([
            ':id_user' => $userId
        ]);

        $patient = $stmt->fetch();

        return $patient ?: null;
    }
    /**
     * Crea la ficha inicial de un paciente asociada a su usuario.
     */
    public function create(int $userId): int
    {
        // La ficha se crea inicialmente con los datos personales vacíos.
        $sql = "
            INSERT INTO pacientes (
                id_user,
                fecha_nac,
                genero,
                direccion
            ) VALUES (
                :id_user,
                NULL,
                NULL,
                NULL
            )
        ";

        $stmt = $this->database->prepare($sql);

        $stmt->execute([
            ':id_user' => $userId
        ]);

        return (int) $this->database->lastInsertId();
    }
}
