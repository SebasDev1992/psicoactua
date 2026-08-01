<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

/**
 * Controlador del panel del paciente.
 */
class PatientController
{
    /**
     * Muestra el panel principal del paciente.
     */
    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        require_once __DIR__ . '/../views/patient/index.php';
    }
}
