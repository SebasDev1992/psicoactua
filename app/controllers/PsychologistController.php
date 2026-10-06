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

    public function createAvailability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];
        $dayOfWeek = (int) ($_POST['dia_semana'] ?? 0);
        $startTime = trim($_POST['hora_inicio'] ?? '');
        $endTime = trim($_POST['hora_fin'] ?? '');

        $errors = [];

        // Valida el día seleccionado.
        if ($dayOfWeek < 1 || $dayOfWeek > 6) {
            $errors[] = 'Selecciona un día válido.';
        }

        // Valida el formato recibido desde el formulario.
        if (
            !preg_match('/^\d{2}:\d{2}$/', $startTime) ||
            !preg_match('/^\d{2}:\d{2}$/', $endTime)
        ) {
            $errors[] = 'Las horas seleccionadas no son válidas.';
        }

        // Comprueba que el rango tenga sentido.
        if (empty($errors)) {
            $start = DateTime::createFromFormat('H:i', $startTime);
            $end = DateTime::createFromFormat('H:i', $endTime);

            if ($start === false || $end === false) {
                $errors[] = 'Las horas seleccionadas no son válidas.';
            } elseif ($start >= $end) {
                $errors[] = 'La hora de inicio debe ser menor que la hora de fin.';
            }
        }
            // Detiene el proceso si los datos ya son inválidos.
        if (!empty($errors)) {
            $_SESSION['availability_errors'] = $errors;
            header('Location: /psicologo/disponibilidad');
            exit;
        }

        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        $availabilityModel = new Availability();

        // Evita cruces con otros horarios activos.
        if (
            empty($errors) &&
            $availabilityModel->hasOverlap(
                (int) $psychologist['id_psi'],
                $dayOfWeek,
                $startTime,
                $endTime
            )
        ) {
            $errors[] = 'El horario se cruza con otro horario existente.';
        }

        if (!empty($errors)) {
            $_SESSION['availability_errors'] = $errors;
            header('Location: /psicologo/disponibilidad');
            exit;
        }

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
