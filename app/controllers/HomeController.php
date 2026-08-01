<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class HomeController
{
    public function index()
    {
        AuthMiddleware::handle();
        $title = 'Inicio | Psico Actúa';

        ob_start();

        require_once __DIR__ . '/../../resources/views/home/index.php';

        $content = ob_get_clean();

        require_once __DIR__ . '/../../resources/views/layouts/app.php';
    }
}
