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
        // La búsqueda respalda tanto el login como la verificación de correos repetidos.
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

        // El marcador evita interpolar el correo directamente en la consulta.
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
    /**
     * Registra un nuevo usuario en la base de datos.
     *
     * @return int ID del usuario creado.
     */
    public function create(array $data): int
    {
        // Inserta las credenciales y datos básicos que forman la cuenta de usuario.
        $query = '
            INSERT INTO users (
                id_rol,
                nom,
                ape,
                email,
                password,
                tel,
                estado
            ) VALUES (
                :role_id,
                :name,
                :last_name,
                :email,
                :password,
                :phone,
                :status
            )
        ';

        $statement = $this->database->prepare($query);

        $statement->execute([
            'role_id' => $data['role_id'],
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'status' => $data['status'],
        ]);

        return (int) $this->database->lastInsertId();
    }
}
