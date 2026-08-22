<?php

/**
 * Clase base para los modelos de la aplicación.
 *
 * Centraliza el acceso a la base de datos para que cada
 * modelo pueda reutilizar la misma conexión PDO.
 */
abstract class Model
{
    /**
     * Conexión activa con la base de datos.
     */
    protected PDO $database;

    /**
     * Inicializa la conexión del modelo.
     *
     * Si recibe una conexión existente, la reutiliza.
     * Esto permite compartir una misma transacción entre modelos.
     */
    public function __construct(?PDO $database = null)
    {
        if ($database !== null) {
            $this->database = $database;
            return;
        }

        require_once __DIR__ . '/database.php';

        $connection = new Database();

        $this->database = $connection->connection();
    }
}

