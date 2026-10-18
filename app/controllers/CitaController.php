<?php

require_once __DIR__ . '/../models/Psychologist.php';
require_once __DIR__ . '/../services/CitaService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class CitaController
{
    /**
     * Muestra los psicólogos disponibles para agendar.
     */
    public function seleccionarPsicologo(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        $psychologistModel = new Psychologist();
        $psicologos = $psychologistModel->getActivePsychologists();

        require_once __DIR__ . '/../views/patient/agendar.php';
    }

    /**
     * Muestra el calendario del psicólogo seleccionado.
     */
    public function mostrarCalendario(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        $idPsicologo = filter_var(
            $_GET['id_psi'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($idPsicologo === false || $idPsicologo === null || $idPsicologo < 1) {
            http_response_code(400);
            exit('El psicólogo seleccionado no es válido.');
        }

        $psychologistModel = new Psychologist();

        $psicologo = $psychologistModel->findActiveById($idPsicologo);

        if ($psicologo === null) {
            http_response_code(404);
            exit('No se encontró el psicólogo seleccionado.');
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../views/patient/calendario.php';
    }

    /**
     * Devuelve los horarios disponibles para una fecha.
     */
    public function horariosDisponibles(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        $psychologistId = filter_input(
            INPUT_GET,
            'id_psi',
            FILTER_VALIDATE_INT
        );

        $date = trim($_GET['fecha'] ?? '');

        if (
            $psychologistId === false ||
            $psychologistId === null ||
            $psychologistId < 1
        ) {
            $this->jsonResponse([
                'ok' => false,
                'mensaje' => 'El psicólogo seleccionado no es válido.',
            ], 400);
        }

        try {
            $service = new CitaService();

            $horarios = $service->obtenerHorariosDisponibles(
                (int) $psychologistId,
                $date
            );

            $timezone = new DateTimeZone('America/Bogota');

            $fecha = DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $date,
                $timezone
            );

            $diaSemana = $fecha !== false
                ? (int) $fecha->format('N')
                : null;

            $this->jsonResponse([
                'ok' => true,
                'fecha' => $date,
                'dia_semana' => $diaSemana,
                'duracion_minutos' => 60,
                'horarios' => $horarios,
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->jsonResponse([
                'ok' => false,
                'mensaje' => $exception->getMessage(),
            ], 422);
        } catch (RuntimeException $exception) {
            $this->jsonResponse([
                'ok' => false,
                'mensaje' => $exception->getMessage(),
            ], 404);
        }
    }

    /**
     * Registra la solicitud de cita del paciente.
     */
    public function solicitarCita(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle([3]);

        $idPsicologo = filter_var(
            $_POST['id_psi'] ?? null,
            FILTER_VALIDATE_INT
        );

        $fecha = trim($_POST['fecha'] ?? '');
        $hora = trim($_POST['hora'] ?? '');

        $urlCalendario = '/paciente/agendar/calendario?id_psi=' . (int) $idPsicologo;

        if ($idPsicologo === false || $idPsicologo === null || $idPsicologo < 1) {
            $this->redirectWithError(
                '/paciente/agendar',
                'El psicólogo seleccionado no es válido.'
            );
        }

        $token = $_POST['_token'] ?? '';

        if (
            !isset($_SESSION['csrf_token']) ||
            !is_string($token) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            $this->redirectWithError(
                $urlCalendario,
                'La solicitud ha caducado. Recarga el calendario e inténtalo nuevamente.'
            );
        }

        try {
            $service = new CitaService();

            $service->solicitarCita(
                (int) $_SESSION['user']['id'],
                (int) $idPsicologo,
                $fecha,
                $hora
            );

            $_SESSION['cita_success'] =
                'Solicitud enviada correctamente. Tu cita está pendiente de confirmación por el psicólogo.';

            header('Location: ' . $urlCalendario);
            exit;
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $this->redirectWithError(
                $urlCalendario,
                $exception->getMessage()
            );
        } catch (PDOException $exception) {
            error_log(
                'No se pudo crear la cita: ' . $exception->getMessage()
            );

            $this->redirectWithError(
                $urlCalendario,
                'No fue posible reservar ese horario. Puede que se haya ocupado; inténtalo nuevamente.'
            );
        }
    }

    /**
     * Redirige al calendario mostrando un mensaje de error.
     */
    private function redirectWithError(
        string $url,
        string $message
    ): void {
        $_SESSION['cita_errors'] = [$message];

        header('Location: ' . $url);
        exit;
    }

    /**
     * Envía una respuesta JSON.
     */
    private function jsonResponse(
        array $data,
        int $statusCode = 200
    ): void {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, max-age=0');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }
}
