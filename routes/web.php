<?php
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PatientController.php';
require_once __DIR__ . '/../app/controllers/PsychologistController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';



/**
 * Rutas públicas de la aplicación.
 */
$router->get('/', [HomeController::class, 'index']);

// El flujo de autenticación comienza con el formulario y sus envíos POST.
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/registro', [AuthController::class, 'showRegister']);
$router->post('/registro', [AuthController::class, 'register']);

// Cada panel delega su protección de sesión y rol al controlador.
$router->get('/paciente', [PatientController::class, 'index']);
$router->get('/psicologo', [PsychologistController::class, 'index']);
$router->get('/administrador', [AdminController::class, 'index']);
// Muestra los datos personales del paciente autenticado.
$router->get('/paciente/perfil', [PatientController::class, 'profile']);
$router->post('/paciente/perfil', [PatientController::class, 'updateProfile']);

// Permite al psicólogo consultar sus horarios de disponibilidad.
$router->get('/psicologo/disponibilidad', [PsychologistController::class, 'availability']);

// Permite al psicólogo crear un nuevo horario.
$router->post('/psicologo/disponibilidad', [PsychologistController::class, 'createAvailability']);

// Permite al psicólogo desactivar uno de sus horarios activos.
$router->post(
    '/psicologo/disponibilidad/desactivar',
    [PsychologistController::class, 'deactivateAvailability']
);
