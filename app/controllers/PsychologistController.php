<?php

require_once __DIR__ . '/../models/Psychologist.php';
require_once __DIR__ . '/../models/Availability.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

/**
 * Controlador del panel y disponibilidad del psicólogo.
 */
class PsychologistController
{
    /**
     * Muestra el panel principal del psicólogo.
     */
    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        require_once __DIR__ . '/../views/psychologist/index.php';
    }

    /**
     * Muestra la disponibilidad del psicólogo autenticado.
     */
    public function availability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];

        // Busca el registro del psicólogo asociado al usuario actual.
        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        // Consulta los horarios activos del psicólogo.
        $availabilityModel = new Availability();

        $availability = $availabilityModel->getByPsychologistId(
            (int) $psychologist['id_psi']
        );

        require_once __DIR__ . '/../views/psychologist/availability.php';
    }

    /**
     * Procesa la creación de un nuevo horario.
     */
    public function createAvailability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];

        $dayOfWeek = (int) ($_POST['dia_semana'] ?? 0);
        $startTime = trim($_POST['hora_inicio'] ?? '');
        $endTime = trim($_POST['hora_fin'] ?? '');

        // Busca al psicólogo relacionado con el usuario autenticado.
        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        $availabilityModel = new Availability();

        $availabilityModel->create(
            (int) $psychologist['id_psi'],
            $dayOfWeek,
            $startTime,
            $endTime
        );

        $_SESSION['success'] = 'Horario creado correctamente.';

        header('Location: /psicologo/disponibilidad');
        exit;
    }
}