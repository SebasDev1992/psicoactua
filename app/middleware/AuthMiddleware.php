<?php

/**
 * Protege las rutas que requieren un usuario autenticado.
 */
class AuthMiddleware
{
    /**
     * Verifica si existe una sesión activa.
     */
    public static function handle(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['login_errors'] = [
                'Debes iniciar sesión para acceder a esta página.',
            ];

            header('Location: /login');
            exit;
        }
    }
}
