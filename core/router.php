<?php

class Router
{
    /**
     * Almacena acciones separadas por método HTTP y ruta exacta.
     */
    private array $routes = [];

    /**
     * Registra una ruta destinada a mostrar información.
     */
    public function get(string $path, callable|array $action): void
    {
        $this->routes['GET'][$path] = $action;
    }
    /**
     * Registra una ruta que recibe solicitudes POST.
     */
    public function post(string $path, callable|array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Descarta los parámetros de consulta al buscar la ruta registrada.
        $path = parse_url($uri, PHP_URL_PATH);

        $action = $this->routes[$method][$path] ?? null;

        if (!$action) {
            http_response_code(404);

            echo '<h1>Error 404</h1>';
            echo '<p>La página solicitada no existe.</p>';

            return;
        }

        if (is_array($action)) {
            // Las rutas de controlador se resuelven creando su instancia al atender la petición.
            [$controller, $method] = $action;

            $instance = new $controller();
            $instance->$method();

            return;
        }

        $action();
    }
}
