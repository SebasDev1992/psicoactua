/**
 * Genera las opciones de hora permitidas para configurar
 * la disponibilidad semanal del psicólogo.
 *
 * Las horas se muestran en formato de 12 horas, pero sus
 * valores se envían en formato HH:MM para el backend.
 */
document.addEventListener('DOMContentLoaded', () => {
    const selectorDia = document.querySelector('#dia_semana');
    const selectorInicio = document.querySelector('#hora_inicio');
    const selectorFin = document.querySelector('#hora_fin');

    // Evita ejecutar la lógica si esta pantalla no contiene el formulario.
    if (!selectorDia || !selectorInicio || !selectorFin) {
        return;
    }

    /**
     * Convierte los minutos del día en una hora legible.
     * Ejemplo: 360 -> 6:00 AM.
     */
    const formatearHora = (minutos) => {
        const hora24 = Math.floor(minutos / 60);
        const minutosHora = minutos % 60;
        const periodo = hora24 >= 12 ? 'PM' : 'AM';
        const hora12 = hora24 % 12 || 12;

        return `${hora12}:${String(minutosHora).padStart(2, '0')} ${periodo}`;
    };

    /**
     * Crea una opción con el valor HH:MM y su etiqueta AM/PM.
     */
    const agregarOpcion = (selector, minutos) => {
        const hora24 = String(Math.floor(minutos / 60)).padStart(2, '0');
        const minutosHora = String(minutos % 60).padStart(2, '0');

        const opcion = new Option(
            formatearHora(minutos),
            `${hora24}:${minutosHora}`
        );

        selector.add(opcion);
    };

    /**
     * Reinicia un selector y controla si puede utilizarse.
     */
    const reiniciarSelector = (selector, texto, deshabilitado = true) => {
        selector.replaceChildren(new Option(texto, ''));
        selector.value = '';
        selector.disabled = deshabilitado;
    };

    /**
     * Devuelve los límites laborales según el día seleccionado.
     *
     * Lunes a viernes: 6:00 AM - 6:00 PM.
     * Sábado:           7:00 AM - 2:00 PM.
     */
    const obtenerJornada = () => {
        const dia = Number(selectorDia.value);

        if (dia >= 1 && dia <= 5) {
            return {
                inicio: 6 * 60,
                fin: 18 * 60,
            };
        }

        if (dia === 6) {
            return {
                inicio: 7 * 60,
                fin: 14 * 60,
            };
        }

        return null;
    };

    /**
     * Actualiza las horas de finalización según la hora de inicio.
     * La hora de fin siempre debe ser posterior a la de inicio.
     */
    const actualizarHorasFinalizacion = () => {
        const jornada = obtenerJornada();
        const horaInicio = selectorInicio.value;

        reiniciarSelector(
            selectorFin,
            'Selecciona la hora de finalización'
        );

        if (!jornada || !horaInicio) {
            return;
        }

        const [horas, minutos] = horaInicio.split(':').map(Number);
        const inicioEnMinutos = horas * 60 + minutos;

        // Solo permite horas posteriores al inicio y dentro de la jornada.
        for (
            let hora = inicioEnMinutos + 60;
            hora <= jornada.fin;
            hora += 60
        ) {
            agregarOpcion(selectorFin, hora);
        }

        selectorFin.disabled = false;
    };

    /**
     * Regenera las opciones cada vez que cambia el día.
     */
    const actualizarJornada = () => {
        const jornada = obtenerJornada();

        reiniciarSelector(
            selectorInicio,
            jornada
                ? 'Selecciona la hora de inicio'
                : 'Primero selecciona un día',
            !jornada
        );

        reiniciarSelector(
            selectorFin,
            'Selecciona primero la hora de inicio'
        );

        if (!jornada) {
            return;
        }

        // Una cita debe terminar dentro de la jornada laboral.
        // Por eso la hora de inicio no puede ser igual a la hora de cierre.
        for (
            let hora = jornada.inicio;
            hora < jornada.fin;
            hora += 60
        ) {
            agregarOpcion(selectorInicio, hora);
        }
    };

    selectorDia.addEventListener('change', actualizarJornada);
    selectorInicio.addEventListener('change', actualizarHorasFinalizacion);

    // Deja los campos preparados según el día inicial del formulario.
    actualizarJornada();
});
