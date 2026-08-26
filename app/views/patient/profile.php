<?php
/**
 * Datos de usuario y paciente recuperados por PatientController::profile().
 *
 * @var array $patient
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

    <title>Mi perfil | PsicoActúa</title>

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
                    Mi perfil
                </h1>
            </div>

            <a
                href="/paciente"
                class="rounded-lg border border-slate-600
                       px-4 py-2 text-sm font-semibold
                       transition hover:bg-slate-800"
            >
                Volver
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-6 py-10">

        <div class="rounded-2xl bg-white p-8 shadow-sm">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">
                    Información personal
                </h2>

                <p class="mt-2 text-sm text-slate-600">
                    Consulta la información registrada en PsicoActúa.
                </p>
            </div>

                <form
                    method="POST"
                    action="/paciente/perfil"
                    class="grid gap-6 md:grid-cols-2"
                >
                <div>
                <p class="text-sm font-medium text-slate-500">
                            Nombres
                </p>

                        <p class="mt-1 text-lg text-slate-900">
                <?= htmlspecialchars(
                                $patient['nom'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                </p>
                </div>

                    <div>
                <p class="text-sm font-medium text-slate-500">
                            Apellidos
                </p>

                        <p class="mt-1 text-lg text-slate-900">
                <?= htmlspecialchars(
                                $patient['ape'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                </p>
                </div>

                    <div>
                <p class="text-sm font-medium text-slate-500">
                            Correo electrónico
                </p>

                        <p class="mt-1 text-lg text-slate-900">
                <?= htmlspecialchars(
                                $patient['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                </p>
                </div>

                    <div>
                <p class="text-sm font-medium text-slate-500">
                            Teléfono
                </p>

                        <p class="mt-1 text-lg text-slate-900">
                <?= htmlspecialchars(
                                $patient['tel'] ?? 'No registrado',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                </p>
                </div>

                    <div>
                <label
                            for="fecha_nac"
                            class="text-sm font-medium text-slate-500"
                >
                            Fecha de nacimiento
                </label>

                        <input
                            type="date"
                            id="fecha_nac"
                            name="fecha_nac"
                            value="<?= htmlspecialchars(
                                $patient['fecha_nac'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="mt-1 w-full rounded-lg border border-slate-300
                                px-4 py-2 text-slate-900"
                >
                </div>

                    <div>
                <label
                            for="genero"
                            class="text-sm font-medium text-slate-500"
                >
                            Género
                </label>

                        <input
                            type="text"
                            id="genero"
                            name="genero"
                            value="<?= htmlspecialchars(
                                $patient['genero'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="mt-1 w-full rounded-lg border border-slate-300
                                px-4 py-2 text-slate-900"
                >
                </div>

                    <div class="md:col-span-2">
                <label
                            for="direccion"
                            class="text-sm font-medium text-slate-500"
                >
                            Dirección
                </label>

                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            value="<?= htmlspecialchars(
                                $patient['direccion'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="mt-1 w-full rounded-lg border border-slate-300
                                px-4 py-2 text-slate-900"
                >
                </div>

                    <div class="md:col-span-2">
                <button
                    type="submit"
                    style="background-color: #0891b2; color: white;"
                    class="rounded-lg px-5 py-3 font-semibold transition"
                >
                    Guardar cambios
                </button>
                </div>
            </form>

    </main>

</body>
</html>
