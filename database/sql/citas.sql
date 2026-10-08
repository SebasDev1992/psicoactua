-- Ajustes de la tabla citas para el módulo de agendamiento.
-- Las citas requieren fecha, hora y un estado inicial.
-- Los índices mejoran las consultas de disponibilidad.

ALTER TABLE citas
    MODIFY fecha DATE NOT NULL,
    MODIFY hora TIME NOT NULL,
    MODIFY estado VARCHAR(20) NOT NULL DEFAULT 'pendiente';

-- Facilita comprobar si un psicólogo ya tiene
-- una cita en una fecha y hora determinada.
ALTER TABLE citas
    ADD KEY citas_psicologo_fecha_hora (id_psi, fecha, hora);

-- Facilita comprobar si un paciente ya tiene
-- una cita en una fecha y hora determinada.
ALTER TABLE citas
    ADD KEY citas_paciente_fecha_hora (id_pac, fecha, hora);
    