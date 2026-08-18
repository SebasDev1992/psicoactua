<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

/**
 * Controlador del panel del psicólogo.
 */
class PsychologistController
{
    /**
     * Carga el panel reservado para usuarios con rol de psicólogo.
     */
    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        require_once __DIR__ . '/../views/psychologist/index.php';
    }
}
