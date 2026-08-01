<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

/**
 * Controlador del panel administrativo.
 */
class AdminController
{
    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([1]);

        require_once __DIR__ . '/../views/admin/index.php';
    }
}
