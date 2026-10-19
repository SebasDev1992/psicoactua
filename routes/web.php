<?php

/*
 * Controladores principales de la aplicación.
 *
 * Cada controlador contiene la lógica correspondiente
 * a un módulo específico del sistema.
 */
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PatientController.php';
require_once __DIR__ . '/../app/controllers/PsychologistController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/CitaController.php';


/**
 * =========================================================
 * RUTAS PÚBLICAS
 * =========================================================
 *
 * Estas rutas pueden ser consultadas sin iniciar sesión.
 */

/**
 * Página principal de la aplicación.
 */
$router->get('/', [HomeController::class, 'index']);


/**
 * =========================================================
 * AUTENTICACIÓN
 * =========================================================
 *
 * Gestiona el inicio de sesión, cierre de sesión y registro
 * de nuevos usuarios.
 */

// Muestra el formulario de inicio de sesión.
$router->get('/login', [AuthController::class, 'showLogin']);

// Procesa las credenciales enviadas desde el formulario de login.
$router->post('/login', [AuthController::class, 'login']);

// Cierra la sesión del usuario autenticado.
$router->post('/logout', [AuthController::class, 'logout']);

// Muestra el formulario de registro.
$router->get('/registro', [AuthController::class, 'showRegister']);

// Procesa el registro de un nuevo usuario.
$router->post('/registro', [AuthController::class, 'register']);


/**
 * =========================================================
 * PANELES PRINCIPALES
 * =========================================================
 *
 * Cada panel delega al controlador la protección mediante
 * autenticación y validación del rol correspondiente.
 */

// Muestra el panel principal del paciente.
$router->get('/paciente', [PatientController::class, 'index']);

// Muestra el panel principal del psicólogo.
$router->get('/psicologo', [PsychologistController::class, 'index']);

// Muestra el panel principal del administrador.
$router->get('/administrador', [AdminController::class, 'index']);


/**
 * =========================================================
 * PERFIL DEL PACIENTE
 * =========================================================
 *
 * Permite al paciente consultar y actualizar sus datos personales.
 */

// Muestra los datos personales del paciente autenticado.
$router->get('/paciente/perfil', [PatientController::class, 'profile']);

// Procesa la actualización de los datos personales del paciente.
$router->post('/paciente/perfil', [PatientController::class, 'updateProfile']);


/**
 * =========================================================
 * AGENDA DEL PSICÓLOGO
 * =========================================================
 *
 * Permite al psicólogo consultar sus citas y gestionar
 * las solicitudes pendientes.
 */

// Muestra la agenda del psicólogo autenticado.
$router->get('/psicologo/agenda', [PsychologistController::class, 'agenda']);

// Permite confirmar o cancelar una cita pendiente.
$router->post(
    '/psicologo/agenda/estado',
    [PsychologistController::class, 'actualizarEstadoCita']
);


/**
 * =========================================================
 * DISPONIBILIDAD DEL PSICÓLOGO
 * =========================================================
 *
 * Permite al psicólogo configurar los días y horarios
 * en los que puede atender pacientes.
 */

// Muestra los horarios de disponibilidad del psicólogo.
$router->get(
    '/psicologo/disponibilidad',
    [PsychologistController::class, 'availability']
);

// Permite al psicólogo crear un nuevo horario de disponibilidad.
$router->post(
    '/psicologo/disponibilidad',
    [PsychologistController::class, 'createAvailability']
);

// Permite al psicólogo desactivar uno de sus horarios activos.
$router->post(
    '/psicologo/disponibilidad/desactivar',
    [PsychologistController::class, 'deactivateAvailability']
);


/**
 * =========================================================
 * AGENDAMIENTO DE CITAS
 * =========================================================
 *
 * Permite al paciente seleccionar un psicólogo, consultar
 * horarios disponibles y solicitar una cita.
 */

// Muestra los psicólogos activos para iniciar el agendamiento.
$router->get(
    '/paciente/agendar',
    [CitaController::class, 'seleccionarPsicologo']
);

// Muestra el calendario del psicólogo seleccionado.
$router->get(
    '/paciente/agendar/calendario',
    [CitaController::class, 'mostrarCalendario']
);

// Consulta dinámicamente los horarios disponibles para una fecha.
$router->get(
    '/paciente/agendar/horarios',
    [CitaController::class, 'horariosDisponibles']
);

// Registra la solicitud de cita realizada por el paciente.
$router->post(
    '/paciente/agendar/solicitar',
    [CitaController::class, 'solicitarCita']
);