<?php

/**
 * Controla el acceso a las rutas según el rol del usuario.
 */
class RoleMiddleware
{
    /**
     * Permite el acceso únicamente a los roles indicados.
     *
     * @param int[] $allowedRoles
     */
    public static function handle(array $allowedRoles): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $userRole = (int) $_SESSION['user']['role_id'];

        if (!in_array($userRole, $allowedRoles, true)) {
            http_response_code(403);

            exit('Acceso no autorizado.');
        }
    }
}
