<?php
// La sesión ya fue validada por el controlador antes de cargar esta vista.
$user = $_SESSION['user'];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel del paciente | PsicoActúa</title>

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
                    Panel del paciente
                </h1>
            </div>

            <!-- Envía el cierre de sesión mediante la ruta POST para finalizar la sesión actual. -->
            <form action="/logout" method="POST">
                <button
                    type="submit"
                    class="rounded-lg border border-slate-600
                           px-4 py-2 text-sm font-semibold
                           transition hover:bg-slate-800"
                >
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-10">

        <section class="mb-8">
            <p class="text-slate-600">
                Bienvenido,
            </p>

            <!-- Escapa el nombre almacenado en sesión antes de insertarlo en el HTML. -->
            <h2 class="text-3xl font-bold text-slate-900">
                <?= htmlspecialchars(
                    $user['name'] . ' ' . $user['last_name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h2>
        </section>
        <?php if ($success !== null): ?>
            <div class="mb-8 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

            <a
                href="#"
                class="rounded-2xl bg-white p-6 shadow-sm
                       transition hover:-translate-y-1 hover:shadow-md"
            >
                <h3 class="text-lg font-semibold text-slate-900">
                    Agendar cita
                </h3>

                <p class="mt-2 text-sm text-slate-600">
                    Consulta fechas y horarios disponibles.
                </p>
            </a>

            <a
                href="#"
                class="rounded-2xl bg-white p-6 shadow-sm
                       transition hover:-translate-y-1 hover:shadow-md"
            >
                <h3 class="text-lg font-semibold text-slate-900">
                    Citas programadas
                </h3>

                <p class="mt-2 text-sm text-slate-600">
                    Revisa tus próximas citas.
                </p>
            </a>

            <a
                href="#"
                class="rounded-2xl bg-white p-6 shadow-sm
                       transition hover:-translate-y-1 hover:shadow-md"
            >
                <h3 class="text-lg font-semibold text-slate-900">
                    Historial de citas
                </h3>

                <p class="mt-2 text-sm text-slate-600">
                    Consulta las sesiones anteriores.
                </p>
            </a>

            <!-- Esta tarjeta conecta el dashboard con la consulta de perfil del paciente. -->
            <a
                href="/paciente/perfil"
                class="rounded-2xl bg-white p-6 shadow-sm
                       transition hover:-translate-y-1 hover:shadow-md"
            >
                <h3 class="text-lg font-semibold text-slate-900">
                    Mi perfil
                </h3>

                <p class="mt-2 text-sm text-slate-600">
                    Actualiza tu información personal.
                </p>
            </a>

        </section>

    </main>

</body>
</html>
