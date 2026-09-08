<?php

/**
 * Horarios del psicólogo recuperados por el controlador.
 *
 * @var array $availability
 * @var array $psychologist
 */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Disponibilidad | PsicoActúa</title>

    <link
        rel="stylesheet"
        href="/assets/css/app.css"
    >
</head>

<body class="min-h-screen bg-slate-100">

    <header class="bg-slate-900 text-white">
        <div
            class="mx-auto flex max-w-7xl items-center
                   justify-between px-6 py-5"
        >
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">
                    Psicoagenda
                </p>

                <h1 class="mt-1 text-xl font-bold">
                    Mi disponibilidad
                </h1>
            </div>

            <a
                href="/psicologo"
                class="rounded-lg border border-slate-600
                       px-4 py-2 text-sm font-semibold
                       transition hover:bg-slate-800"
            >
                Volver
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">

        <?php if (isset($_SESSION['success'])): ?>

            <div
                class="mb-6 rounded-lg border border-green-200
                       bg-green-50 px-4 py-3 text-sm
                       font-medium text-green-800"
            >
                <?= htmlspecialchars(
                    $_SESSION['success'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900">
                Configura tus horarios de atención
            </h2>

            <p class="mt-2 text-slate-600">
                <?= htmlspecialchars(
                    $psychologist['especialidad'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>
        </section>

        <section
            class="mb-8 rounded-2xl bg-white p-8 shadow-sm"
        >
            <h3 class="mb-6 text-xl font-semibold text-slate-900">
                Nuevo horario
            </h3>

            <form
                action="/psicologo/disponibilidad"
                method="POST"
                class="grid gap-6 md:grid-cols-3"
            >

                <div>
                    <label
                        for="dia_semana"
                        class="block text-sm font-medium text-slate-600"
                    >
                        Día
                    </label>

                    <select
                        id="dia_semana"
                        name="dia_semana"
                        required
                        class="mt-1 w-full rounded-lg border
                               border-slate-300 px-4 py-2"
                    >
                        <option value="">Selecciona</option>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </div>

                <div>
                    <label
                        for="hora_inicio"
                        class="block text-sm font-medium text-slate-600"
                    >
                        Hora de inicio
                    </label>

                    <input
                        type="time"
                        id="hora_inicio"
                        name="hora_inicio"
                        required
                        class="mt-1 w-full rounded-lg border
                               border-slate-300 px-4 py-2"
                    >
                </div>

                <div>
                    <label
                        for="hora_fin"
                        class="block text-sm font-medium text-slate-600"
                    >
                        Hora de finalización
                    </label>

                    <input
                        type="time"
                        id="hora_fin"
                        name="hora_fin"
                        required
                        class="mt-1 w-full rounded-lg border
                               border-slate-300 px-4 py-2"
                    >
                </div>

                <div class="md:col-span-3">
                    <button
                        type="submit"
                        class="rounded-lg bg-cyan-600
                               px-5 py-3 font-semibold text-white
                               transition hover:bg-cyan-700"
                    >
                        Agregar horario
                    </button>
                </div>

            </form>
        </section>

        <section class="rounded-2xl bg-white p-8 shadow-sm">

            <h3 class="mb-6 text-xl font-semibold text-slate-900">
                Horarios registrados
            </h3>

            <?php if ($availability === []): ?>

                <p class="text-slate-600">
                    Todavía no tienes horarios registrados.
                </p>

            <?php else: ?>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="px-4 py-3 text-sm font-semibold">
                                    Día
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Inicio
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Fin
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Estado
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $days = [
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                7 => 'Domingo',
                            ];
                            ?>

                            <?php foreach ($availability as $schedule): ?>

                                <tr class="border-b border-slate-100">

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars(
                                            $days[$schedule['dia_semana']] ?? 'Desconocido',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars(
                                            $schedule['hora_inicio'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        <?= htmlspecialchars(
                                            $schedule['hora_fin'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-4 py-3">
                                        Activo
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>