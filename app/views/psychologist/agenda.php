<?php

/**
 * Vista de la agenda del psicólogo.
 *
 * Variables recibidas desde el controlador:
 * @var array $citas
 */

// Recupera los mensajes temporales enviados por el controlador.
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

// Elimina los mensajes para que solo aparezcan una vez.
unset($_SESSION['success'], $_SESSION['error']);

// Obtiene la fecha y hora actual para distinguir las citas próximas.
$zonaHoraria = new DateTimeZone('America/Bogota');
$ahora = new DateTimeImmutable('now', $zonaHoraria);

// Separa las citas según su estado y fecha.
$citasPendientes = [];
$citasProximas = [];

foreach ($citas as $cita) {

    // Combina la fecha y la hora almacenadas en la base de datos.
    $fechaHoraCita = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        $cita['fecha'] . ' ' . $cita['hora'],
        $zonaHoraria
    );

    if ($fechaHoraCita === false) {
        continue;
    }

    // Las citas pendientes requieren una acción del psicólogo.
    if ($cita['estado'] === 'pendiente') {
        $citasPendientes[] = $cita;
        continue;
    }

    // Las citas confirmadas que todavía no han ocurrido
    // se muestran como próximas citas.
    if (
        $cita['estado'] === 'confirmada' &&
        $fechaHoraCita >= $ahora
    ) {
        $citasProximas[] = $cita;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mi agenda | PsicoActúa</title>

    <link
        rel="stylesheet"
        href="/assets/css/app.css"
    >
</head>

<body class="min-h-screen bg-slate-100">

    <!-- Encabezado principal de la sección. -->
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
                    Mi agenda
                </h1>

            </div>

            <!-- Regresa al panel principal del psicólogo. -->
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


    <main class="mx-auto max-w-6xl px-6 py-10">

        <!-- Mensaje mostrado después de confirmar o cancelar una cita. -->
        <?php if ($success !== null): ?>

            <div
                class="mb-6 rounded-lg border border-green-200
                       bg-green-50 px-4 py-3 text-sm
                       font-medium text-green-800"
            >
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <!-- Mensaje mostrado cuando ocurre un error. -->
        <?php if ($error !== null): ?>

            <div
                class="mb-6 rounded-lg border border-red-200
                       bg-red-50 px-4 py-3 text-sm
                       font-medium text-red-700"
            >
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <!-- =====================================================
             SOLICITUDES PENDIENTES
             ===================================================== -->

        <section class="mb-8 rounded-2xl bg-white p-8 shadow-sm">

            <div class="mb-6">

                <h2 class="text-2xl font-bold text-slate-900">
                    Solicitudes pendientes
                </h2>

                <p class="mt-2 text-slate-600">
                    Revisa y gestiona las solicitudes de cita realizadas
                    por tus pacientes.
                </p>

            </div>


            <?php if ($citasPendientes === []): ?>

                <div
                    class="rounded-lg border border-slate-200
                           bg-slate-50 px-4 py-5 text-slate-600"
                >
                    No tienes solicitudes de cita pendientes.
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>

                            <tr class="border-b border-slate-200">

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Paciente
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Fecha
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Hora
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Estado
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($citasPendientes as $cita): ?>

                                <tr class="border-b border-slate-100">

                                    <!-- Nombre completo del paciente. -->
                                    <td class="px-4 py-4">

                                        <?= htmlspecialchars(
                                            $cita['nom'] . ' ' . $cita['ape'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- Fecha de la cita. -->
                                    <td class="px-4 py-4">

                                        <?php
                                        $fecha = DateTimeImmutable::createFromFormat(
                                            'Y-m-d',
                                            $cita['fecha'],
                                            $zonaHoraria
                                        );
                                        ?>

                                        <?= $fecha
                                            ? htmlspecialchars(
                                                $fecha->format('d/m/Y'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Fecha no válida'
                                        ?>

                                    </td>


                                    <!-- Hora de la cita en formato de 12 horas. -->
                                    <td class="px-4 py-4">

                                        <?php
                                        $hora = DateTimeImmutable::createFromFormat(
                                            'H:i:s',
                                            $cita['hora'],
                                            $zonaHoraria
                                        );
                                        ?>

                                        <?= $hora
                                            ? htmlspecialchars(
                                                $hora->format('g:i A'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Hora no válida'
                                        ?>

                                    </td>


                                    <!-- Estado actual de la solicitud. -->
                                    <td class="px-4 py-4">

                                        <span
                                            class="rounded-full bg-yellow-100
                                                   px-3 py-1 text-xs font-semibold
                                                   text-yellow-800"
                                        >
                                            Pendiente
                                        </span>

                                    </td>


                                    <!-- Acciones disponibles para una cita pendiente. -->
                                    <td class="px-4 py-4">

                                        <div class="flex flex-wrap gap-2">

                                            <!-- Formulario para confirmar la cita. -->
                                            <form
                                                action="/psicologo/agenda/estado"
                                                method="POST"
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
                                                    name="id_cita"
                                                    value="<?= (int) $cita['id_cita'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="confirmada"
                                                >

                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-green-600
                                                           px-3 py-2 text-sm
                                                           font-semibold text-white
                                                           transition hover:bg-green-700"
                                                >
                                                    Confirmar
                                                </button>

                                            </form>


                                            <!-- Formulario para cancelar la cita. -->
                                            <form
                                                action="/psicologo/agenda/estado"
                                                method="POST"
                                                onsubmit="return confirm(
                                                    '¿Deseas cancelar esta cita?'
                                                );"
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
                                                    name="id_cita"
                                                    value="<?= (int) $cita['id_cita'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="estado"
                                                    value="cancelada"
                                                >

                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-red-600
                                                           px-3 py-2 text-sm
                                                           font-semibold text-white
                                                           transition hover:bg-red-700"
                                                >
                                                    Cancelar
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>


        <!-- =====================================================
             PRÓXIMAS CITAS CONFIRMADAS
             ===================================================== -->

        <section class="rounded-2xl bg-white p-8 shadow-sm">

            <div class="mb-6">

                <h2 class="text-2xl font-bold text-slate-900">
                    Próximas citas
                </h2>

                <p class="mt-2 text-slate-600">
                    Citas confirmadas que todavía están pendientes de atención.
                </p>

            </div>


            <?php if ($citasProximas === []): ?>

                <div
                    class="rounded-lg border border-slate-200
                           bg-slate-50 px-4 py-5 text-slate-600"
                >
                    No tienes próximas citas confirmadas.
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>

                            <tr class="border-b border-slate-200">

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Paciente
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Fecha
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Hora
                                </th>

                                <th class="px-4 py-3 text-sm font-semibold">
                                    Estado
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($citasProximas as $cita): ?>

                                <tr class="border-b border-slate-100">

                                    <!-- Nombre completo del paciente. -->
                                    <td class="px-4 py-4">

                                        <?= htmlspecialchars(
                                            $cita['nom'] . ' ' . $cita['ape'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- Fecha de la cita. -->
                                    <td class="px-4 py-4">

                                        <?php
                                        $fecha = DateTimeImmutable::createFromFormat(
                                            'Y-m-d',
                                            $cita['fecha'],
                                            $zonaHoraria
                                        );
                                        ?>

                                        <?= $fecha
                                            ? htmlspecialchars(
                                                $fecha->format('d/m/Y'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Fecha no válida'
                                        ?>

                                    </td>


                                    <!-- Hora de la cita en formato de 12 horas. -->
                                    <td class="px-4 py-4">

                                        <?php
                                        $hora = DateTimeImmutable::createFromFormat(
                                            'H:i:s',
                                            $cita['hora'],
                                            $zonaHoraria
                                        );
                                        ?>

                                        <?= $hora
                                            ? htmlspecialchars(
                                                $hora->format('g:i A'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : 'Hora no válida'
                                        ?>

                                    </td>


                                    <!-- Estado de la cita. -->
                                    <td class="px-4 py-4">

                                        <span
                                            class="rounded-full bg-green-100
                                                   px-3 py-1 text-xs font-semibold
                                                   text-green-800"
                                        >
                                            Confirmada
                                        </span>

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