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

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/registro', [AuthController::class, 'showRegister']);
$router->post('/registro', [AuthController::class, 'register']);
$router->get('/paciente', [PatientController::class, 'index']);
$router->get('/psicologo', [PsychologistController::class, 'index']);
$router->get('/administrador', [AdminController::class, 'index']);
