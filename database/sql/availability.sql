-- Tabla que almacena los horarios de atención de los psicólogos.
-- dia_semana: 1 = lunes, 2 = martes, ..., 6 = sábado.
-- estado: 1 = activo, 0 = inactivo.

CREATE TABLE disponibilidad (
    id_disponibilidad INT(11) NOT NULL AUTO_INCREMENT,
    id_psi INT(11) NOT NULL,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado TINYINT NOT NULL DEFAULT 1,

    PRIMARY KEY (id_disponibilidad),

    KEY disponibilidad_psicologos_FK (id_psi),

    CONSTRAINT disponibilidad_psicologos_FK
        FOREIGN KEY (id_psi)
        REFERENCES psicologos (id_psi)
);