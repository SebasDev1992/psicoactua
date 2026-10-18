<?php

require_once __DIR__ . '/../models/Psychologist.php';
require_once __DIR__ . '/../models/Availability.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Patient.php';

class CitaService
{
    private const DURACION_CITA = 60;
    private const DIAS_AGENDAMIENTO = 60;
    private const ZONA_HORARIA = 'America/Bogota';

    /**
     * Obtiene los horarios disponibles para una fecha.
     */
    public function obtenerHorariosDisponibles(
        int $psychologistId,
        string $date
    ): array {
        $timezone = new DateTimeZone(self::ZONA_HORARIA);

        $selectedDate = $this->validarFecha($date, $timezone);

        $dayOfWeek = (int) $selectedDate->format('N');

        if ($dayOfWeek === 7) {
            return [];
        }

        $psychologistModel = new Psychologist();

        if ($psychologistModel->findActiveById($psychologistId) === null) {
            throw new RuntimeException(
                'El psicólogo seleccionado no está disponible.'
            );
        }

        $availabilityModel = new Availability();

        $availability = $availabilityModel->getByPsychologistAndDay(
            $psychologistId,
            $dayOfWeek
        );

        if ($availability === []) {
            return [];
        }

        $citaModel = new Cita();

        $occupiedTimes = $citaModel->getOccupiedTimes(
            $psychologistId,
            $date
        );

        $occupied = array_fill_keys(
            array_map(
                fn($time) => substr((string) $time, 0, 8),
                $occupiedTimes
            ),
            true
        );

        $workingHours = $this->obtenerJornadaLaboral($dayOfWeek);

        $availableTimes = [];

        $now = new DateTimeImmutable('now', $timezone);
        $today = new DateTimeImmutable('today', $timezone);

        foreach ($availability as $schedule) {
            $startTime = max(
                substr((string) $schedule['hora_inicio'], 0, 8),
                $workingHours['inicio']
            );

            $endTime = min(
                substr((string) $schedule['hora_fin'], 0, 8),
                $workingHours['fin']
            );

            if ($startTime >= $endTime) {
                continue;
            }

            $start = DateTimeImmutable::createFromFormat(
                '!H:i:s',
                $startTime,
                $timezone
            );

            $end = DateTimeImmutable::createFromFormat(
                '!H:i:s',
                $endTime,
                $timezone
            );

            if ($start === false || $end === false) {
                continue;
            }

            while (true) {
                $slotEnd = $start->modify(
                    '+' . self::DURACION_CITA . ' minutes'
                );

                if ($slotEnd > $end) {
                    break;
                }

                $slotTime = $start->format('H:i:s');

                $slotDateTime = $selectedDate->setTime(
                    (int) $start->format('H'),
                    (int) $start->format('i')
                );

                if (
                    $selectedDate == $today &&
                    $slotDateTime <= $now
                ) {
                    $start = $slotEnd;
                    continue;
                }

                if (!isset($occupied[$slotTime])) {
                    $availableTimes[] = $slotTime;
                }

                $start = $slotEnd;
            }
        }

        $availableTimes = array_values(
            array_unique($availableTimes)
        );

        sort($availableTimes);

        return $availableTimes;
    }

    /**
     * Registra una solicitud de cita.
     */
    public function solicitarCita(
        int $userId,
        int $psychologistId,
        string $date,
        string $time
    ): void {
        $timezone = new DateTimeZone(self::ZONA_HORARIA);

        $selectedDate = $this->validarFecha($date, $timezone);

        $this->validarHora($time, $timezone);

        $dayOfWeek = (int) $selectedDate->format('N');

        if ($dayOfWeek === 7) {
            throw new InvalidArgumentException(
                'No hay atención disponible los domingos.'
            );
        }

        $psychologistModel = new Psychologist();

        if ($psychologistModel->findActiveById($psychologistId) === null) {
            throw new RuntimeException(
                'El psicólogo seleccionado ya no está disponible.'
            );
        }

        $patientModel = new Patient();

        $patient = $patientModel->findByUserId($userId);

        if ($patient === null) {
            throw new RuntimeException(
                'No se encontró el perfil del paciente.'
            );
        }

        $workingHours = $this->obtenerJornadaLaboral($dayOfWeek);

        $horaFinCita = DateTimeImmutable::createFromFormat(
            '!H:i:s',
            $time,
            $timezone
        )->modify('+' . self::DURACION_CITA . ' minutes');

        if (
            $time < $workingHours['inicio'] ||
            $horaFinCita->format('H:i:s') > $workingHours['fin']
        ) {
            throw new InvalidArgumentException(
                'El horario seleccionado está fuera de la jornada laboral.'
            );
        }

        $ahora = new DateTimeImmutable('now', $timezone);

        $inicioCita = $selectedDate->setTime(
            (int) substr($time, 0, 2),
            (int) substr($time, 3, 2),
            (int) substr($time, 6, 2)
        );

        if ($selectedDate == new DateTimeImmutable('today', $timezone)) {
            if ($inicioCita <= $ahora) {
                throw new InvalidArgumentException(
                    'La hora seleccionada ya pasó.'
                );
            }
        }

        $horariosDisponibles = $this->obtenerHorariosDisponibles(
            $psychologistId,
            $date
        );

        if (!in_array($time, $horariosDisponibles, true)) {
            throw new RuntimeException(
                'Ese horario ya no está disponible. Selecciona otro.'
            );
        }

        $citaModel = new Cita();

        if (
            $citaModel->isTimeOccupied(
                $psychologistId,
                $date,
                $time
            )
        ) {
            throw new RuntimeException(
                'Ese horario acaba de ocuparse. Selecciona otro.'
            );
        }

        if (
            $citaModel->patientHasAppointment(
                (int) $patient['id_pac'],
                $date,
                $time
            )
        ) {
            throw new RuntimeException(
                'Ya tienes una cita en esa fecha y hora.'
            );
        }

        $citaModel->create(
            (int) $patient['id_pac'],
            $psychologistId,
            $date,
            $time
        );
    }

    /**
     * Valida una fecha dentro del periodo permitido.
     */
    private function validarFecha(
        string $date,
        DateTimeZone $timezone
    ): DateTimeImmutable {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException(
                'La fecha seleccionada no es válida.'
            );
        }

        $selectedDate = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $date,
            $timezone
        );

        $errors = DateTimeImmutable::getLastErrors();

        if (
            $selectedDate === false ||
            (
                is_array($errors) &&
                (
                    $errors['warning_count'] > 0 ||
                    $errors['error_count'] > 0
                )
            ) ||
            (
                $selectedDate !== false &&
                $selectedDate->format('Y-m-d') !== $date
            )
        ) {
            throw new InvalidArgumentException(
                'La fecha seleccionada no existe.'
            );
        }

        $today = new DateTimeImmutable('today', $timezone);

        $limit = $today->modify(
            '+' . self::DIAS_AGENDAMIENTO . ' days'
        );

        if ($selectedDate < $today || $selectedDate > $limit) {
            throw new InvalidArgumentException(
                'La fecha debe estar dentro de los próximos 60 días.'
            );
        }

        return $selectedDate;
    }

    /**
     * Valida la hora recibida.
     */
    private function validarHora(
        string $time,
        DateTimeZone $timezone
    ): DateTimeImmutable {
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            throw new InvalidArgumentException(
                'La hora seleccionada no es válida.'
            );
        }

        $hora = DateTimeImmutable::createFromFormat(
            '!H:i:s',
            $time,
            $timezone
        );

        $errors = DateTimeImmutable::getLastErrors();

        if (
            $hora === false ||
            (
                is_array($errors) &&
                (
                    $errors['warning_count'] > 0 ||
                    $errors['error_count'] > 0
                )
            ) ||
            (
                $hora !== false &&
                $hora->format('H:i:s') !== $time
            )
        ) {
            throw new InvalidArgumentException(
                'La hora seleccionada no es válida.'
            );
        }

        return $hora;
    }

    /**
     * Devuelve la jornada laboral permitida.
     */
    private function obtenerJornadaLaboral(int $dayOfWeek): array
    {
        return $dayOfWeek === 6
            ? [
                'inicio' => '07:00:00',
                'fin' => '14:00:00',
            ]
            : [
                'inicio' => '06:00:00',
                'fin' => '18:00:00',
            ];
    }
}
