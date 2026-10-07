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
     * Muestra el panel principal del psicólogo autenticado.
     */
    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        require_once __DIR__ . '/../views/psychologist/index.php';
    }

    /**
     * Muestra los horarios de disponibilidad del psicólogo autenticado.
     */
    public function availability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];

        // Obtiene el perfil profesional asociado al usuario autenticado.
        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        // Recupera únicamente los horarios activos del psicólogo.
        $availabilityModel = new Availability();

        $availability = $availabilityModel->getByPsychologistId(
            (int) $psychologist['id_psi']
        );

        require_once __DIR__ . '/../views/psychologist/availability.php';
    }

    /**
     * Valida y crea un nuevo horario de disponibilidad.
     */
    public function createAvailability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];
        $dayOfWeek = (int) ($_POST['dia_semana'] ?? 0);
        $startTime = trim($_POST['hora_inicio'] ?? '');
        $endTime = trim($_POST['hora_fin'] ?? '');

        $errors = [];

        // Valida que el día corresponda a lunes hasta sábado.
        if ($dayOfWeek < 1 || $dayOfWeek > 6) {
            $errors[] = 'Selecciona un día válido.';
        }

        // Valida que las horas tengan el formato HH:MM esperado.
        if (
            !preg_match('/^\d{2}:\d{2}$/', $startTime) ||
            !preg_match('/^\d{2}:\d{2}$/', $endTime)
        ) {
            $errors[] = 'Las horas seleccionadas no son válidas.';
        }

        // Comprueba que las horas sean válidas y que el rango sea correcto.
        if (empty($errors)) {
            $start = DateTime::createFromFormat('H:i', $startTime);
            $end = DateTime::createFromFormat('H:i', $endTime);

            if ($start === false || $end === false) {
                $errors[] = 'Las horas seleccionadas no son válidas.';
            } elseif ($start >= $end) {
                $errors[] = 'La hora de inicio debe ser menor que la hora de fin.';
            }
        }

        // Si los datos básicos son inválidos, detiene el proceso.
        if (!empty($errors)) {
            $_SESSION['availability_errors'] = $errors;
            header('Location: /psicologo/disponibilidad');
            exit;
        }

        // Obtiene el psicólogo asociado al usuario autenticado.
        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        $psychologistId = (int) $psychologist['id_psi'];
        $availabilityModel = new Availability();

        // Evita crear un horario que se cruce con otro horario activo.
        if (
            $availabilityModel->hasOverlap(
                $psychologistId,
                $dayOfWeek,
                $startTime,
                $endTime
            )
        ) {
            $_SESSION['availability_errors'] = [
                'El horario se cruza con otro horario existente.',
            ];

            header('Location: /psicologo/disponibilidad');
            exit;
        }

        // Guarda el nuevo horario únicamente cuando todas las validaciones pasan.
        $availabilityModel->create(
            $psychologistId,
            $dayOfWeek,
            $startTime,
            $endTime
        );

        $_SESSION['success'] = 'Horario creado correctamente.';

        header('Location: /psicologo/disponibilidad');
        exit;
    }

    /**
     * Desactiva un horario existente sin eliminarlo de la base de datos.
     */
    public function deactivateAvailability(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];
        $availabilityId = (int) ($_POST['id_disponibilidad'] ?? 0);

        // Verifica que se haya recibido un identificador válido.
        if ($availabilityId <= 0) {
            $_SESSION['availability_errors'] = [
                'El horario seleccionado no es válido.',
            ];

            header('Location: /psicologo/disponibilidad');
            exit;
        }

        // Obtiene el psicólogo correspondiente al usuario autenticado.
        $psychologistModel = new Psychologist();
        $psychologist = $psychologistModel->findByUserId($userId);

        if ($psychologist === null) {
            http_response_code(404);
            exit('No se encontró la información del psicólogo.');
        }

        $availabilityModel = new Availability();

        // Desactiva únicamente un horario perteneciente al psicólogo actual.
        $availabilityModel->deactivate(
            (int) $psychologist['id_psi'],
            $availabilityId
        );

        $_SESSION['success'] = 'Horario desactivado correctamente.';

        header('Location: /psicologo/disponibilidad');
        exit;
    }
}
