<?php

class Router
{
    private array $routes = [];

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
        $path = parse_url($uri, PHP_URL_PATH);

        $action = $this->routes[$method][$path] ?? null;

        if (!$action) {
            http_response_code(404);

            echo '<h1>Error 404</h1>';
            echo '<p>La página solicitada no existe.</p>';

            return;
        }

        if (is_array($action)) {
            [$controller, $method] = $action;

            $instance = new $controller();
            $instance->$method();

            return;
        }

        $action();
    }
}