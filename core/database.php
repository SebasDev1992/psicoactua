<?php

/**
 * Gestiona la conexión con la base de datos mediante PDO.
 *
 * Esta clase centraliza la conexión para evitar repetir
 * credenciales y configuraciones en cada modelo.
 */
class Database
{
    /**
     * Conexión activa con la base de datos.
     */
    private PDO $connection;

    /**
     * Crea la conexión al instanciar la clase.
     */
    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            /**
             * Durante desarrollo mostramos un mensaje entendible.
             * En producción este detalle se guardará en un log.
             */
            exit(
                'No fue posible conectar con la base de datos: '
                . $exception->getMessage()
            );
        }
    }

    /**
     * Devuelve la conexión PDO para ejecutar consultas.
     */
    public function connection(): PDO
    {
        return $this->connection;
    }
}
