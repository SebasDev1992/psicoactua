<?php

require_once __DIR__ . '/../models/Psychologist.php';
require_once __DIR__ . '/../models/Availability.php';
require_once __DIR__ . '/../services/CitaService.php';
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
     * Muestra la agenda del psicólogo autenticado.
     */
    public function agenda(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $userId = (int) $_SESSION['user']['id'];

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $citaService = new CitaService();

        try {
            $citas = $citaService->obtenerAgendaPsicologo($userId);
        } catch (RuntimeException $e) {
            http_response_code(404);
            exit($e->getMessage());
        }

        require_once __DIR__ . '/../views/psychologist/agenda.php';
    }

    /**
     * Actualiza el estado de una cita desde la agenda.
     */
    public function actualizarEstadoCita(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([2]);

        $token = $_POST['_token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Solicitud no válida.');
        }

        $userId = (int) $_SESSION['user']['id'];
        $idCita = (int) ($_POST['id_cita'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');

        if ($idCita <= 0) {
            $_SESSION['error'] = 'La cita seleccionada no es válida.';
            header('Location: /psicologo/agenda');
            exit;
        }

        $citaService = new CitaService();

        try {
            $citaService->actualizarEstadoCita(
                $userId,
                $idCita,
                $estado
            );

            $_SESSION['success'] = $estado === 'confirmada'
                ? 'Cita confirmada correctamente.'
                : 'Cita cancelada correctamente.';
        } catch (InvalidArgumentException | RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /psicologo/agenda');
        exit;
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

        // Valida que el día corresponda de lunes a sábado.
        if ($dayOfWeek < 1 || $dayOfWeek > 6) {
            $errors[] = 'Selecciona un día válido.';
        }

        // Valida que las horas tengan el formato HH:MM.
        if (
            !preg_match('/^\d{2}:\d{2}$/', $startTime) ||
            !preg_match('/^\d{2}:\d{2}$/', $endTime)
        ) {
            $errors[] = 'Las horas seleccionadas no son válidas.';
        }

        // Comprueba que las horas sean válidas y que el rango sea correcto.
        if (empty($errors)) {
            $start = DateTime::createFromFormat('!H:i', $startTime);
            $startErrors = DateTime::getLastErrors();

            $end = DateTime::createFromFormat('!H:i', $endTime);
            $endErrors = DateTime::getLastErrors();

            // Evita aceptar valores de hora que PHP haya normalizado.
            $startInvalid = (
                $start === false ||
                $start->format('H:i') !== $startTime ||
                (
                    is_array($startErrors) &&
                    (
                        $startErrors['warning_count'] > 0 ||
                        $startErrors['error_count'] > 0
                    )
                )
            );

            $endInvalid = (
                $end === false ||
                $end->format('H:i') !== $endTime ||
                (
                    is_array($endErrors) &&
                    (
                        $endErrors['warning_count'] > 0 ||
                        $endErrors['error_count'] > 0
                    )
                )
            );

            if ($startInvalid || $endInvalid) {
                $errors[] = 'Las horas seleccionadas no son válidas.';
            } elseif ($start >= $end) {
                $errors[] = 'La hora de inicio debe ser menor que la hora de fin.';
            }
        }

        /*
         * Aplica la jornada laboral del consultorio.
         *
         * Lunes a viernes: 06:00–18:00.
         * Sábados:         07:00–14:00.
         *
         * Los límites son inclusivos: se permite que un horario
         * termine exactamente a las 18:00 o a las 14:00.
         */
        if (empty($errors)) {
            $horaInicioLaboral = $dayOfWeek === 6
                ? '07:00'
                : '06:00';

            $horaFinLaboral = $dayOfWeek === 6
                ? '14:00'
                : '18:00';

            if (
                $startTime < $horaInicioLaboral ||
                $endTime > $horaFinLaboral
            ) {
                $errors[] = $dayOfWeek === 6
                    ? 'Los sábados, el horario debe estar entre las 7:00 AM y las 2:00 PM.'
                    : 'De lunes a viernes, el horario debe estar entre las 6:00 AM y las 6:00 PM.';
            }
        }

        // Detiene el proceso si alguna validación ha fallado.
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