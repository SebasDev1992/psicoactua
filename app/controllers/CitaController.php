<?php

require_once __DIR__ . '/../models/Psychologist.php';
require_once __DIR__ . '/../models/Availability.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

/**
 * Controlador encargado del agendamiento de citas.
 *
 * Coordina la disponibilidad del psicólogo con las citas
 * existentes para determinar qué horarios puede solicitar
 * un paciente.
 */
class CitaController
{
    /**
     * Duración fija de cada cita en minutos.
     */
    private const DURACION_CITA = 60;

    /**
     * Cantidad máxima de días que pueden consultarse hacia adelante.
     */
    private const DIAS_AGENDAMIENTO = 60;

    /**
     * Zona horaria utilizada por la aplicación.
     */
    private const ZONA_HORARIA = 'America/Bogota';

    /**
     * Obtiene los horarios disponibles de un psicólogo
     * para una fecha determinada.
     *
     * La respuesta se entrega en JSON para que la interfaz
     * pueda actualizar los horarios sin recargar toda la página.
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

        // Valida que se haya recibido un psicólogo válido.
        if ($psychologistId === false || $psychologistId === null) {
            $this->jsonResponse(
                [
                    'ok' => false,
                    'mensaje' => 'El psicólogo seleccionado no es válido.',
                ],
                400
            );
        }

        // Valida el formato ISO de la fecha: YYYY-MM-DD.
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->jsonResponse(
                [
                    'ok' => false,
                    'mensaje' => 'La fecha seleccionada no es válida.',
                ],
                400
            );
        }

        $timezone = new DateTimeZone(self::ZONA_HORARIA);

        $today = new DateTimeImmutable(
            'today',
            $timezone
        );

        $selectedDate = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $date,
            $timezone
        );

        $dateErrors = DateTimeImmutable::getLastErrors();

        // Detecta fechas inexistentes, por ejemplo 2026-02-31.
        if (
            $selectedDate === false ||
            (
                is_array($dateErrors) &&
                ($dateErrors['warning_count'] > 0 ||
                $dateErrors['error_count'] > 0)
            )
        ) {
            $this->jsonResponse(
                [
                    'ok' => false,
                    'mensaje' => 'La fecha seleccionada no existe.',
                ],
                400
            );
        }

        $lastAllowedDate = $today->modify(
            '+' . self::DIAS_AGENDAMIENTO . ' days'
        );

        // Impide fechas anteriores a hoy o fuera de la ventana permitida.
        if (
            $selectedDate < $today ||
            $selectedDate > $lastAllowedDate
        ) {
            $this->jsonResponse(
                [
                    'ok' => false,
                    'mensaje' => 'La fecha debe estar dentro de los próximos 60 días.',
                ],
                422
            );
        }

        // ISO-8601: 1 = lunes ... 7 = domingo.
        $dayOfWeek = (int) $selectedDate->format('N');

        // PsicoActúa permite agendamiento de lunes a sábado.
        if ($dayOfWeek > 6) {
            $this->jsonResponse(
                [
                    'ok' => true,
                    'fecha' => $date,
                    'horarios' => [],
                    'mensaje' => 'No hay atención disponible los domingos.',
                ]
            );
        }

        // Verifica que el psicólogo exista y esté activo.
        $psychologistModel = new Psychologist();

        $psychologist = $psychologistModel->findActiveById(
            (int) $psychologistId
        );

        if ($psychologist === null) {
            $this->jsonResponse(
                [
                    'ok' => false,
                    'mensaje' => 'El psicólogo seleccionado no está disponible.',
                ],
                404
            );
        }

        // Obtiene las ventanas de disponibilidad para ese día.
        $availabilityModel = new Availability();

        $availability = $availabilityModel->getByPsychologistAndDay(
            (int) $psychologistId,
            $dayOfWeek
        );

        if ($availability === []) {
            $this->jsonResponse([
                'ok' => true,
                'fecha' => $date,
                'horarios' => [],
                'mensaje' => 'El psicólogo no tiene disponibilidad para esta fecha.',
            ]);
        }

        // Recupera las horas que ya están ocupadas por otras citas.
        $citaModel = new Cita();

        $occupiedTimes = $citaModel->getOccupiedTimes(
            (int) $psychologistId,
            $date
        );

        // Convierte las horas ocupadas en un conjunto para buscarlas rápidamente.
        $occupied = [];

        foreach ($occupiedTimes as $time) {
            $occupied[substr($time, 0, 8)] = true;
        }

        $availableTimes = [];

        $now = new DateTimeImmutable(
            'now',
            $timezone
        );

        foreach ($availability as $schedule) {
            $start = DateTimeImmutable::createFromFormat(
                'H:i:s',
                $schedule['hora_inicio'],
                $timezone
            );

            $end = DateTimeImmutable::createFromFormat(
                'H:i:s',
                $schedule['hora_fin'],
                $timezone
            );

            if ($start === false || $end === false) {
                continue;
            }

            /*
             * Genera bloques consecutivos de 60 minutos.
             *
             * El último bloque solamente se crea si termina
             * dentro del horario de disponibilidad.
             */
            while (true) {
                $slotEnd = $start->modify(
                    '+' . self::DURACION_CITA . ' minutes'
                );

                if ($slotEnd > $end) {
                    break;
                }

                $slotTime = $start->format('H:i:s');

                // Para hoy, descarta horas que ya pasaron.
                $slotDateTime = $selectedDate->setTime(
                    (int) $start->format('H'),
                    (int) $start->format('i'),
                    (int) $start->format('s')
                );

                if (
                    $selectedDate == $today &&
                    $slotDateTime <= $now
                ) {
                    $start = $slotEnd;
                    continue;
                }

                // No muestra una hora que ya esté reservada.
                if (!isset($occupied[$slotTime])) {
                    $availableTimes[] = $slotTime;
                }

                $start = $slotEnd;
            }
        }

        // Elimina posibles duplicados y conserva el orden cronológico.
        $availableTimes = array_values(
            array_unique($availableTimes)
        );

        sort($availableTimes);

        $this->jsonResponse([
            'ok' => true,
            'fecha' => $date,
            'dia_semana' => $dayOfWeek,
            'duracion_minutos' => self::DURACION_CITA,
            'horarios' => $availableTimes,
        ]);
    }

    /**
     * Devuelve una respuesta JSON y finaliza la ejecución.
     */
    private function jsonResponse(
        array $data,
        int $statusCode = 200
    ): void {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }
}