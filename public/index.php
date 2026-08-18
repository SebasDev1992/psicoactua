<?php
/**
 * Inicia la sesión para conservar la autenticación
 * del usuario entre diferentes páginas.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Punto de entrada principal de PsicoActúa.
 *
 * Todas las solicitudes pasan por este archivo y son
 * enviadas al Router para determinar qué controlador usar.
 */

require_once __DIR__ . '/../core/router.php';

$router = new Router();

require_once __DIR__ . '/../routes/web.php';

// Entrega al enrutador la solicitud original para elegir la acción registrada.
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
