<?php

require_once __DIR__ . '/../../core/model.php';

/**
 * Gestiona las operaciones relacionadas con los usuarios.
 */
class User extends Model
{
    /**
     * Busca un usuario por su correo electrónico.
     *
     * @return array|null Retorna el usuario o null si no existe.
     */
    public function findByEmail(string $email): ?array
    {
        $query = '
            SELECT
                id_user,
                id_rol,
                nom,
                ape,
                email,
                password,
                tel,
                estado
            FROM users
            WHERE email = :email
            LIMIT 1
        ';

        $statement = $this->database->prepare($query);

        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch();

        return $user ?: null;
    }

    /**
     * Comprueba si un correo electrónico ya está registrado.
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }
}
