<?php

/**
 * Calendario de agendamiento del paciente.
 *
 * Recibe $psicologo desde CitaController y permite consultar
 * fechas y horarios mediante el servicio de disponibilidad.
 */

$nombrePsicologo = trim(
    ($psicologo['nom'] ?? '') . ' ' .
    ($psicologo['ape'] ?? '')
);

$especialidad = $psicologo['especialidad'] ?? 'Especialidad no especificada';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Consulta las fechas y horarios disponibles para tu cita psicológica."
    >

    <title>Calendario de citas | PsicoActúa</title>

    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- Controla el calendario y la consulta dinámica de horarios. -->
    <script src="/assets/js/calendario.js" defer></script>
</head>

<body
    class="min-h-screen bg-slate-50 text-slate-800"
    data-psychologist-id="<?= (int) $psicologo['id_psi'] ?>"
>

    <!-- Navegación de la pantalla. -->
    <header class="sticky top-0 z-20 border-b border-white/10 bg-slate-950/95 text-white backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a
                href="/paciente"
                class="flex items-center gap-3"
                aria-label="Volver al panel del paciente"
            >
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 shadow-lg">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3a7 7 0 0 0-4 12.75V19h8v-3.25A7 7 0 0 0 12 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 22h6M9 7.5a3 3 0 0 1 3-2.5"/>
                    </svg>
                </span>

                <span>
                    <span class="block text-lg font-bold tracking-tight">
                        PsicoActúa
                    </span>

                    <span class="block text-xs text-slate-400">
                        Agendamiento de citas
                    </span>
                </span>
            </a>

            <a
                href="/paciente/agendar"
                class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-400"
            >
                Cambiar psicólogo
            </a>

        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        <!-- Introducción y pasos del proceso. -->
        <section class="mb-8">

            <p class="text-sm font-semibold uppercase tracking-wider text-cyan-700">
                Paso 2 de 3
            </p>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                Elige la fecha y la hora
            </h1>

            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                Consulta el calendario del profesional y selecciona
                un horario que se adapte a ti.
            </p>

        </section>

        <!-- Información del profesional seleccionado. -->
        <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-6">

                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-100 to-blue-100 text-cyan-800 ring-1 ring-cyan-200">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="8" r="4"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                    </svg>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">
                        Tu profesional
                    </p>

                    <h2 class="mt-1 text-xl font-bold text-slate-950">
                        <?= htmlspecialchars(
                            $nombrePsicologo,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        <?= htmlspecialchars(
                            $especialidad,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

                <div class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/15">

                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                    Profesional activo

                </div>

            </div>

        </section>

        <!-- Calendario y horarios: una columna en móvil y dos en escritorio. -->
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">

            <!-- Calendario mensual interactivo. -->
            <section
                class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6"
                aria-labelledby="titulo-calendario"
            >

                <div class="flex items-center justify-between gap-3">

                    <div>
                        <h2
                            id="titulo-calendario"
                            class="text-lg font-bold text-slate-950 sm:text-xl"
                        >
                            Selecciona una fecha
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Disponibilidad durante los próximos 60 días.
                        </p>
                    </div>

                </div>

                <!-- Navegación entre meses. -->
                <div class="mt-6 flex items-center justify-between gap-3">

                    <button
                        id="mesAnterior"
                        type="button"
                        aria-label="Mostrar mes anterior"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:border-cyan-300 hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-cyan-500/15 disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m15 18-6-6 6-6"/>
                        </svg>
                    </button>

                    <h3
                        id="mesActual"
                        class="text-base font-bold capitalize text-slate-950 sm:text-lg"
                        aria-live="polite"
                    >
                        Cargando calendario...
                    </h3>

                    <button
                        id="mesSiguiente"
                        type="button"
                        aria-label="Mostrar mes siguiente"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:border-cyan-300 hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-cyan-500/15 disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m9 18 6-6-6-6"/>
                        </svg>
                    </button>

                </div>

                <!-- Encabezados de los días de la semana. -->
                <div
                    class="mt-5 grid grid-cols-7 gap-1 sm:gap-2"
                    aria-hidden="true"
                >
                    <?php foreach (['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $dia): ?>

                        <div class="py-2 text-center text-xs font-bold uppercase text-slate-400 sm:text-sm">
                            <?= $dia ?>
                        </div>

                    <?php endforeach; ?>
                </div>

                <!-- JavaScript genera las fechas según el mes seleccionado. -->
                <div
                    id="diasCalendario"
                    class="grid grid-cols-7 gap-1 sm:gap-2"
                    role="group"
                    aria-label="Días del mes"
                ></div>

                <!-- Leyenda visual del calendario. -->
                <div class="mt-6 flex flex-wrap gap-x-4 gap-y-3 border-t border-slate-100 pt-5">

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="h-3 w-3 rounded-full border-2 border-cyan-500"></span>
                        Hoy
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="h-3 w-3 rounded-full bg-slate-900"></span>
                        Seleccionado
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="h-3 w-3 rounded-full bg-slate-200"></span>
                        No seleccionable
                    </div>

                </div>

                <p class="mt-4 text-xs leading-5 text-slate-500">
                    Los domingos, las fechas pasadas y los días fuera
                    del periodo de agendamiento no se pueden seleccionar.
                    La disponibilidad exacta se consulta al elegir cada fecha.
                </p>

            </section>

            <!-- Horarios que devuelve el backend para la fecha elegida. -->
            <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <h2 class="text-lg font-bold text-slate-950 sm:text-xl">
                            Horarios disponibles
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Selecciona una consulta de 60 minutos.
                        </p>
                    </div>

                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 7v5l3 2"/>
                        </svg>

                    </span>

                </div>

                <div
                    id="mensajeHorarios"
                    class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center"
                    role="status"
                    aria-live="polite"
                >
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            aria-hidden="true"
                        >
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>

                    </div>

                    <p class="mt-4 text-sm font-semibold text-slate-700">
                        Selecciona una fecha
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Consultaremos los horarios que el profesional
                        tiene disponibles ese día.
                    </p>

                </div>

                <!-- Las horas se muestran como botones seleccionables. -->
                <div
                    id="listaHorarios"
                    class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3"
                    aria-label="Horarios de consulta"
                ></div>

                <!-- Resumen dinámico de la selección. -->
                <div class="mt-6 rounded-2xl border border-cyan-100 bg-cyan-50/70 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-800">
                        Resumen de selección
                    </p>

                    <div class="mt-3 space-y-3">

                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm text-slate-600">
                                Fecha
                            </span>

                            <span
                                id="fechaResumen"
                                class="text-right text-sm font-semibold text-slate-900"
                            >
                                Sin seleccionar
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm text-slate-600">
                                Hora
                            </span>

                            <span
                                id="horaResumen"
                                class="text-right text-sm font-semibold text-slate-900"
                            >
                                Sin seleccionar
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-3 border-t border-cyan-100 pt-3">
                            <span class="text-sm text-slate-600">
                                Duración
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                60 minutos
                            </span>
                        </div>

                    </div>

                </div>

                <form
                    id="formSolicitarCita"
                    action="/paciente/agendar/solicitar"
                    method="POST"
                    class="mt-5"
                >
                    <input
                        type="hidden"
                        name="_token"
                        value="<?= htmlspecialchars(
                            $_SESSION['csrf_token'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                    <input
                        type="hidden"
                        id="idPsicologo"
                        name="id_psi"
                        value="<?= (int) $psicologo['id_psi'] ?>"
                    >

                    <input
                        type="hidden"
                        id="fechaCita"
                        name="fecha"
                        value=""
                    >

                    <input
                        type="hidden"
                        id="horaCita"
                        name="hora"
                        value=""
                    >

                    <button
                        id="btnSolicitarCita"
                        type="submit"
                        disabled
                        class="w-full rounded-2xl bg-cyan-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-500/20 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                    >
                        Solicitar cita
                    </button>

                    <p class="mt-3 text-center text-xs leading-5 text-slate-500">
                        La solicitud quedará pendiente hasta ser confirmada por el psicólogo.
                    </p>
                </form>

            </section>

        </div>

    </main>

    <footer class="mt-10 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-center text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">

            <p>PsicoActúa · Atención psicológica</p>

            <p>Tu bienestar importa.</p>

        </div>
    </footer>

</body>

</html>