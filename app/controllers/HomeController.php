<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * Atiende la portada dentro del layout general de la aplicación.
 */
class HomeController
{
    public function index()
    {
        // La portada actual solo está disponible para sesiones autenticadas.
        AuthMiddleware::handle();
        $title = 'Inicio | Psico Actúa';

        // Captura la vista para insertarla después en el layout compartido.
        ob_start();

        require_once __DIR__ . '/../../resources/views/home/index.php';

        $content = ob_get_clean();

        require_once __DIR__ . '/../../resources/views/layouts/app.php';
    }
}
