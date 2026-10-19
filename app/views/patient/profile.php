<?php
/**
 * Datos de usuario y paciente recuperados por PatientController::profile().
 *
 * @var array $patient
 */
?>
<?php
$generosPermitidos = [
    'Masculino',
    'Femenino',
    'No binario',
    'Otro',
    'Prefiero no responder',
];

$generoActual = $patient['genero'] ?? '';

$esGeneroOtro = (
    $generoActual !== '' &&
    !in_array($generoActual, $generosPermitidos, true)
);
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

                    <select
                        id="genero"
                        name="genero"
                        class="mt-1 w-full rounded-lg border border-slate-300
                            px-4 py-2 text-slate-900"
                            >
                        <option value="">Selecciona una opción</option>

                        <option
                            value="Masculino"
                            <?= ($patient['genero'] ?? '') === 'Masculino' ? 'selected' : '' ?>
                            >
                            Masculino
                        </option>

                        <option
                            value="Femenino"
                            <?= ($patient['genero'] ?? '') === 'Femenino' ? 'selected' : '' ?>
                            >
                            Femenino
                        </option>

                        <option
                            value="No binario"
                            <?= ($patient['genero'] ?? '') === 'No binario' ? 'selected' : '' ?>
                            >
                            No binario
                        </option>

                        <option
                        value="Otro"
                        <?= (($patient['genero'] ?? '') === 'Otro' || $esGeneroOtro ) ? 'selected' : '' ?>
                        >
                        Otro
                        </option>

                        <option
                            value="Prefiero no responder"
                            <?= ($patient['genero'] ?? '') === 'Prefiero no responder' ? 'selected' : '' ?>
                        >
                            Prefiero no responder
                        </option>
                    </select>
                    <div
                    id="campoGeneroOtro"
                    class="mt-3 hidden"
                    >
                    <label
                        for="genero_otro"
                        class="text-sm font-medium text-slate-500"
                    >
                        Especifica tu género
                    </label>

                    <input
                        type="text"
                        id="genero_otro"
                        name="genero_otro"
                        maxlength="100"
                        value="<?= htmlspecialchars(
                            $esGeneroOtro ? $generoActual : '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="mt-1 w-full rounded-lg border border-slate-300
                            px-4 py-2 text-slate-900"
                        placeholder="Escribe cómo deseas identificarte"
                    >
                </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const genero = document.querySelector('#genero');
        const campoGeneroOtro = document.querySelector('#campoGeneroOtro');
        const generoOtro = document.querySelector('#genero_otro');

        if (!genero || !campoGeneroOtro || !generoOtro) {
            return;
        }

        const actualizarGeneroOtro = () => {
            const esOtro = genero.value === 'Otro';

            campoGeneroOtro.classList.toggle('hidden', !esOtro);
            generoOtro.required = esOtro;

            if (!esOtro) {
                generoOtro.value = '';
            }
        };

        genero.addEventListener('change', actualizarGeneroOtro);

        actualizarGeneroOtro();
    });
    </script>

</body>
</html>
