/**
 * Controla el calendario mensual y la consulta de horarios.
 *
 * El servidor sigue siendo la autoridad para validar fechas
 * y disponibilidad; JavaScript solo gestiona la interacción visual.
 */
document.addEventListener('DOMContentLoaded', () => {
    const idPsicologo = Number(
        document.body.dataset.psychologistId
    );

    const mesAnterior = document.querySelector('#mesAnterior');
    const mesSiguiente = document.querySelector('#mesSiguiente');
    const mesActual = document.querySelector('#mesActual');
    const diasCalendario = document.querySelector('#diasCalendario');

    const mensajeHorarios = document.querySelector('#mensajeHorarios');
    const listaHorarios = document.querySelector('#listaHorarios');

    const fechaResumen = document.querySelector('#fechaResumen');
    const horaResumen = document.querySelector('#horaResumen');
    const fechaCita = document.querySelector('#fechaCita');
    const horaCita = document.querySelector('#horaCita');
    const btnSolicitarCita = document.querySelector('#btnSolicitarCita');

    if (
        !idPsicologo ||
        !mesAnterior ||
        !mesSiguiente ||
        !mesActual ||
        !diasCalendario ||
        !mensajeHorarios ||
        !listaHorarios ||
        !fechaCita ||
        !horaCita ||
        !btnSolicitarCita
    ) {      
        console.error('No se encontraron los elementos del calendario.');
        return;
        }   

    // Captura la fecha actual y calcula el límite máximo seleccionable.
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    const fechaLimite = new Date(hoy);
    fechaLimite.setDate(fechaLimite.getDate() + 60);

    const fechaISO = (fecha) => {
        const anio = fecha.getFullYear();
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const dia = String(fecha.getDate()).padStart(2, '0');

        return `${anio}-${mes}-${dia}`;
    };

    const fechaInicial = fechaISO(hoy);
    const fechaFinal = fechaISO(fechaLimite);

    let mesVisible = new Date(
        hoy.getFullYear(),
        hoy.getMonth(),
        1
    );

    let fechaSeleccionada = null;
    let horaSeleccionada = null;

    // Convierte una fecha ISO a una fecha local sin desplazamientos horarios.
    const crearFechaLocal = (fecha) => {
        const [anio, mes, dia] = fecha.split('-').map(Number);

        return new Date(anio, mes - 1, dia, 12, 0, 0);
    };

    const compararMeses = (fechaA, fechaB) => {
        return (
            fechaA.getFullYear() === fechaB.getFullYear() &&
            fechaA.getMonth() === fechaB.getMonth()
        );
    };

    const formatearFecha = (fecha) => {
        return crearFechaLocal(fecha).toLocaleDateString('es-CO', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    // Presenta la hora interna de 24 horas como 12 horas con AM/PM.
    const formatearHora = (hora) => {
        const [horaTexto, minutoTexto] = hora.split(':');
        const horaNumero = Number(horaTexto);
        const periodo = horaNumero >= 12 ? 'PM' : 'AM';
        const hora12 = horaNumero % 12 || 12;

        return `${hora12}:${minutoTexto} ${periodo}`;
    };

    const actualizarResumen = () => {
        fechaResumen.textContent = fechaSeleccionada
            ? formatearFecha(fechaSeleccionada)
            : 'Sin seleccionar';

        horaResumen.textContent = horaSeleccionada
            ? formatearHora(horaSeleccionada)
            : 'Sin seleccionar';

            if (fechaCita) {
            fechaCita.value = fechaSeleccionada || '';
        }

        if (horaCita) {
            horaCita.value = horaSeleccionada || '';
        }

        if (btnSolicitarCita) {
            btnSolicitarCita.disabled = !fechaSeleccionada || !horaSeleccionada;
        }
    };

    const mostrarMensaje = (titulo, detalle, cargando = false) => {
        mensajeHorarios.replaceChildren();

        const contenedor = document.createElement('div');
        contenedor.className = 'py-2';

        const encabezado = document.createElement('p');
        encabezado.className = 'text-sm font-semibold text-slate-700';
        encabezado.textContent = titulo;

        const descripcion = document.createElement('p');
        descripcion.className = 'mt-2 text-sm leading-6 text-slate-500';
        descripcion.textContent = detalle;

        contenedor.append(encabezado, descripcion);

        if (cargando) {
            const indicador = document.createElement('div');
            indicador.className = 'mx-auto mb-4 h-6 w-6 animate-spin rounded-full border-2 border-cyan-600 border-t-transparent';
            indicador.setAttribute('aria-hidden', 'true');

            contenedor.prepend(indicador);
        }

        mensajeHorarios.replaceChildren(contenedor);
        mensajeHorarios.classList.remove('hidden');
    };

    const limpiarHorarios = () => {
        listaHorarios.replaceChildren();
        horaSeleccionada = null;
        actualizarResumen();
    };

    const renderizarHorarios = (horarios) => {
        listaHorarios.replaceChildren();

        if (horarios.length === 0) {
            mostrarMensaje(
                'No hay horarios disponibles',
                'Prueba otra fecha para consultar otros espacios de atención.'
            );
            return;
        }

        mensajeHorarios.classList.add('hidden');

        horarios.forEach((hora) => {
            const boton = document.createElement('button');

            boton.type = 'button';
            boton.dataset.hora = hora;

            boton.className = [
                'rounded-xl',
                'border',
                'border-slate-200',
                'bg-white',
                'px-3',
                'py-4',
                'text-sm',
                'font-semibold',
                'text-slate-700',
                'shadow-sm',
                'transition',
                'duration-200',
                'hover:-translate-y-0.5',
                'hover:border-cyan-400',
                'hover:bg-cyan-50',
                'focus:outline-none',
                'focus:ring-4',
                'focus:ring-cyan-500/15',
            ].join(' ');

            boton.textContent = formatearHora(hora);

            boton.addEventListener('click', () => {
                horaSeleccionada = hora;

                // Solo un horario puede quedar seleccionado a la vez.
                listaHorarios.querySelectorAll('button').forEach((otroBoton) => {
                    otroBoton.classList.remove(
                        'border-cyan-700',
                        'bg-cyan-700',
                        'text-white',
                        'shadow-md'
                    );

                    otroBoton.classList.add(
                        'border-slate-200',
                        'bg-white',
                        'text-slate-700'
                    );

                    otroBoton.setAttribute('aria-pressed', 'false');
                });

                boton.classList.remove(
                    'border-slate-200',
                    'bg-white',
                    'text-slate-700'
                );

                boton.classList.add(
                    'border-cyan-700',
                    'bg-cyan-700',
                    'text-white',
                    'shadow-md'
                );

                boton.setAttribute('aria-pressed', 'true');

                actualizarResumen();
            });

            boton.setAttribute('aria-pressed', 'false');

            listaHorarios.appendChild(boton);
        });
    };

    // Solicita al backend los horarios reales de la fecha seleccionada.
    const consultarHorarios = async (fecha) => {
        limpiarHorarios();

        mostrarMensaje(
            'Consultando disponibilidad...',
            'Estamos verificando los horarios del profesional.',
            true
        );

        try {
            const parametros = new URLSearchParams({
                id_psi: String(idPsicologo),
                fecha,
            });

            const respuesta = await fetch(
                `/paciente/agendar/horarios?${parametros.toString()}`,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                }
            );

            const datos = await respuesta.json();

            if (!respuesta.ok || !datos.ok) {
                mostrarMensaje(
                    'No fue posible consultar los horarios',
                    datos.mensaje || 'Inténtalo nuevamente.'
                );
                return;
            }

            renderizarHorarios(datos.horarios || []);
        } catch (error) {
            console.error('Error consultando horarios:', error);

            mostrarMensaje(
                'No pudimos cargar los horarios',
                'Comprueba tu conexión e inténtalo nuevamente.'
            );
        }
    };

    // Dibuja el mes actual con las restricciones básicas del calendario.
    const renderizarCalendario = () => {
        diasCalendario.replaceChildren();

        const anio = mesVisible.getFullYear();
        const mes = mesVisible.getMonth();

        const primerDia = new Date(anio, mes, 1);
        const cantidadDias = new Date(anio, mes + 1, 0).getDate();

        // Convierte domingo=0 en lunes=0 para ordenar la cuadrícula.
        const espaciosIniciales = (primerDia.getDay() + 6) % 7;

        const nombreMes = mesVisible.toLocaleDateString('es-CO', {
            month: 'long',
            year: 'numeric',
        });

        mesActual.textContent =
            nombreMes.charAt(0).toUpperCase() + nombreMes.slice(1);

        const primerMesPermitido = new Date(
            hoy.getFullYear(),
            hoy.getMonth(),
            1
        );

        const ultimoMesPermitido = new Date(
            fechaLimite.getFullYear(),
            fechaLimite.getMonth(),
            1
        );

        mesAnterior.disabled = mesVisible <= primerMesPermitido;
        mesSiguiente.disabled = mesVisible >= ultimoMesPermitido;

        // Mantiene la alineación de los días al inicio del mes.
        for (let espacio = 0; espacio < espaciosIniciales; espacio++) {
            const celdaVacia = document.createElement('div');
            celdaVacia.setAttribute('aria-hidden', 'true');
            diasCalendario.appendChild(celdaVacia);
        }

        for (let numeroDia = 1; numeroDia <= cantidadDias; numeroDia++) {
            const fecha = new Date(anio, mes, numeroDia);
            fecha.setHours(0, 0, 0, 0);

            const fechaTexto = fechaISO(fecha);
            const esHoy = fechaTexto === fechaInicial;
            const esSeleccionada = fechaTexto === fechaSeleccionada;
            const esDomingo = fecha.getDay() === 0;

            const dentroDelPeriodo =
                fechaTexto >= fechaInicial &&
                fechaTexto <= fechaFinal;

            const seleccionable = dentroDelPeriodo && !esDomingo;

            const boton = document.createElement('button');

            boton.type = 'button';
            boton.textContent = String(numeroDia);
            boton.className = [
                'relative',
                'flex',
                'aspect-square',
                'min-h-10',
                'w-full',
                'items-center',
                'justify-center',
                'rounded-xl',
                'border',
                'text-sm',
                'font-semibold',
                'transition',
                'duration-200',
                'focus:outline-none',
                'focus:ring-4',
                'focus:ring-cyan-500/15',
            ].join(' ');

            if (esSeleccionada) {
                boton.classList.add(
                    'border-slate-900',
                    'bg-slate-900',
                    'text-white',
                    'shadow-md'
                );
            } else if (seleccionable) {
                boton.classList.add(
                    'border-slate-100',
                    'bg-white',
                    'text-slate-700',
                    'hover:border-cyan-400',
                    'hover:bg-cyan-50',
                    'hover:shadow-sm'
                );
            } else {
                boton.classList.add(
                    'cursor-not-allowed',
                    'border-transparent',
                    'bg-slate-50',
                    'text-slate-300'
                );

                boton.disabled = true;
            }

            if (esHoy) {
                const indicador = document.createElement('span');

                indicador.className = esSeleccionada
                    ? 'absolute bottom-1 h-1 w-1 rounded-full bg-cyan-300'
                    : 'absolute bottom-1 h-1 w-1 rounded-full bg-cyan-600';

                indicador.setAttribute('aria-hidden', 'true');

                boton.appendChild(indicador);
            }

            boton.setAttribute(
                'aria-label',
                `${formatearFecha(fechaTexto)}${esHoy ? ', hoy' : ''}`
            );

            boton.setAttribute(
                'aria-pressed',
                esSeleccionada ? 'true' : 'false'
            );

            boton.disabled = !seleccionable;

            if (seleccionable) {
                boton.addEventListener('click', () => {
                    fechaSeleccionada = fechaTexto;
                    horaSeleccionada = null;

                    renderizarCalendario();
                    actualizarResumen();
                    consultarHorarios(fechaSeleccionada);
                });
            }

            diasCalendario.appendChild(boton);
        }
    };

    mesAnterior.addEventListener('click', () => {
        if (mesAnterior.disabled) {
            return;
        }

        mesVisible = new Date(
            mesVisible.getFullYear(),
            mesVisible.getMonth() - 1,
            1
        );

        renderizarCalendario();
    });

    mesSiguiente.addEventListener('click', () => {
        if (mesSiguiente.disabled) {
            return;
        }

        mesVisible = new Date(
            mesVisible.getFullYear(),
            mesVisible.getMonth() + 1,
            1
        );

        renderizarCalendario();
    });

    actualizarResumen();
    renderizarCalendario();
});