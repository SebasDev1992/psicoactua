<?php

/**
 * Vista para seleccionar un psicólogo e iniciar el agendamiento.
 *
 * Recibe $psicologos desde CitaController, con los profesionales
 * activos recuperados desde la base de datos.
 */

$psicologos = is_array($psicologos ?? null) ? $psicologos : [];

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
        content="Selecciona un profesional y consulta sus horarios de atención en PsicoActúa."
    >

    <title>Agendar cita | PsicoActúa</title>

    <link
        rel="stylesheet"
        href="/assets/css/app.css"
    >

    <!-- Gestiona la búsqueda dinámica de profesionales. -->
    <script src="/assets/js/agendar.js" defer></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <!-- Navegación principal de la sección de agendamiento. -->
    <header class="sticky top-0 z-20 border-b border-white/10 bg-slate-950/95 text-white backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a
                href="/paciente"
                class="flex items-center gap-3"
                aria-label="Volver al panel de PsicoActúa"
            >
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 shadow-lg shadow-cyan-950/30">
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
                        Portal del paciente
                    </span>
                </span>
            </a>

            <a
                href="/paciente"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-700 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:border-slate-500 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-400"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m15 18-6-6 6-6M9 12h12"/>
                </svg>

                <span class="hidden sm:inline">Volver al panel</span>
                <span class="sm:hidden">Volver</span>
            </a>

        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        <!-- Presentación del proceso de agendamiento. -->
        <section class="relative isolate mb-10 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 px-6 py-9 text-white shadow-xl sm:px-10 sm:py-12">

            <!-- Elementos decorativos sin interferir con la interacción. -->
            <div
                class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"
                aria-hidden="true"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"
                aria-hidden="true"
            ></div>

            <div class="relative max-w-3xl">

                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-cyan-200">
                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                    Agendamiento de citas
                </span>

                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                    Encuentra el profesional indicado para ti
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base sm:leading-7">
                    Explora nuestros profesionales, conoce sus especialidades
                    y selecciona a quién deseas consultar. Después podrás
                    elegir una fecha y un horario disponible.
                </p>

                <!-- Resume las reglas principales del agendamiento. -->
                <div class="mt-7 flex flex-wrap gap-3">

                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-cyan-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18"/>
                        </svg>

                        <span class="text-sm text-slate-200">
                            Agenda hasta con 60 días de anticipación
                        </span>
                    </div>

                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-cyan-300"
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

                        <span class="text-sm text-slate-200">
                            Consultas de 60 minutos
                        </span>
                    </div>

                </div>

            </div>

        </section>

        <!-- Encabezado y búsqueda de profesionales. -->
        <section aria-labelledby="titulo-profesionales">

            <div class="mb-6 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">

                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-cyan-700">
                        Nuestro equipo
                    </p>

                    <h2
                        id="titulo-profesionales"
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl"
                    >
                        Selecciona tu psicólogo
                    </h2>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                        Elige un profesional para consultar sus fechas
                        y horarios de atención.
                    </p>
                </div>

                <?php if (!empty($psicologos)): ?>

                    <!-- La búsqueda filtra las tarjetas sin recargar la página. -->
                    <div class="w-full md:max-w-sm">

                        <label
                            for="buscarPsicologo"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Buscar profesional
                        </label>

                        <div class="relative">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <circle cx="11" cy="11" r="7"/>
                                <path stroke-linecap="round" d="m16 16 4 4"/>
                            </svg>

                            <input
                                id="buscarPsicologo"
                                type="search"
                                placeholder="Nombre o especialidad..."
                                autocomplete="off"
                                aria-describedby="contadorResultados"
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-11 pr-4 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10"
                            >

                        </div>

                    </div>

                <?php endif; ?>

            </div>

            <?php if (empty($psicologos)): ?>

                <!-- Estado vacío cuando no hay profesionales activos. -->
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
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

                    <h3 class="mt-5 text-lg font-semibold text-slate-900">
                        No hay profesionales disponibles
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        En este momento no encontramos psicólogos activos
                        para agendar. Puedes volver a intentarlo más tarde.
                    </p>

                    <a
                        href="/paciente"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
                    >
                        Volver al panel
                    </a>

                </div>

            <?php else: ?>

                <p
                    id="contadorResultados"
                    class="mb-5 text-sm text-slate-500"
                    aria-live="polite"
                >
                    Mostrando <?= count($psicologos) ?>
                    <?= count($psicologos) === 1
                        ? 'profesional activo'
                        : 'profesionales activos' ?>
                </p>

                <!-- Tarjetas generadas con los datos reales de la base de datos. -->
                <div
                    id="listaPsicologos"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3"
                >

                    <?php foreach ($psicologos as $psicologo): ?>

                        <?php
                        $idPsicologo = (int) ($psicologo['id_psi'] ?? 0);

                        $nombre = trim(
                            ($psicologo['nom'] ?? '') . ' ' .
                            ($psicologo['ape'] ?? '')
                        );

                        $especialidad = trim(
                            (string) ($psicologo['especialidad'] ?? '')
                        );

                        if ($especialidad === '') {
                            $especialidad = 'Especialidad no especificada';
                        }

                        $textoBusqueda = $nombre . ' ' . $especialidad;
                        ?>

                        <article
                            data-psychologist-card
                            data-search="<?= htmlspecialchars(
                                $textoBusqueda,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl hover:shadow-slate-200/60 sm:p-6"
                        >

                            <!-- Identidad visual y estado del profesional. -->
                            <div class="flex items-start justify-between gap-3">

                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-100 to-blue-100 text-cyan-800 ring-1 ring-cyan-200/60 transition duration-300 group-hover:scale-105">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-7 w-7"
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

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/15">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        aria-hidden="true"
                                    ></span>

                                    Activo
                                </span>

                            </div>

                            <!-- Datos del profesional: no se inventan reseñas ni calificaciones. -->
                            <div class="mt-5 flex-1">

                                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">
                                    Especialidad
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-600">
                                    <?= htmlspecialchars(
                                        $especialidad,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <h3 class="mt-3 text-xl font-bold tracking-tight text-slate-900 transition group-hover:text-cyan-800">
                                    <?= htmlspecialchars(
                                        $nombre,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h3>

                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Consulta los días de atención y elige
                                    el horario que mejor se adapte a ti.
                                </p>

                            </div>

                            <!-- Navega al calendario del profesional seleccionado. -->
                            <div class="mt-6 border-t border-slate-100 pt-5">

                                <?php if ($idPsicologo > 0): ?>

                                    <a
                                        href="/paciente/agendar/calendario?id_psi=<?= $idPsicologo ?>"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-500/20"
                                    >
                                        Elegir psicólogo

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M5 12h14m-6-6 6 6-6 6"/>
                                        </svg>
                                    </a>

                                <?php else: ?>

                                    <span class="block rounded-xl bg-slate-100 px-4 py-3.5 text-center text-sm font-medium text-slate-500">
                                        Perfil no disponible
                                    </span>

                                <?php endif; ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <!-- Se muestra cuando el buscador no encuentra coincidencias. -->
                <div
                    id="sinResultados"
                    class="mt-6 hidden rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center"
                    role="status"
                >
                    <h3 class="font-semibold text-slate-900">
                        No encontramos coincidencias
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Prueba con otro nombre o una especialidad diferente.
                    </p>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <footer class="mt-10 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-center text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:text-left lg:px-8">

            <p>
                PsicoActúa · Atención psicológica
            </p>

            <p>
                Tu bienestar importa.
            </p>

        </div>
    </footer>

</body>
</html>
