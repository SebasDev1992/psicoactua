<?php

/**
 * Controlador encargado de las vistas y procesos
 * relacionados con la autenticación de usuarios.
 */
class AuthController
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }
}
