<?php

/**
 * Vista pública para el registro de pacientes.
 */
?>
<?php

/**
 * Recupera errores y datos temporales del formulario.
 */
$errors = $_SESSION['register_errors'] ?? [];
$old = $_SESSION['register_old'] ?? [];

unset($_SESSION['register_errors'], $_SESSION['register_old']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Crear cuenta | PsicoActúa</title>

    <link
        rel="stylesheet"
        href="/assets/css/app.css"
    >
</head>

<body class="min-h-screen bg-slate-100">

    <main class="min-h-screen grid lg:grid-cols-2">

        <!-- Información del sistema -->
        <section
            class="hidden lg:flex flex-col justify-center
                   bg-slate-900 px-16 text-white"
        >
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">
                Psicoagenda
            </p>

            <h1 class="mt-4 text-4xl font-bold leading-tight">
                Crea tu cuenta en PsicoActúa
            </h1>

            <p class="mt-6 max-w-lg text-lg text-slate-300">
                Regístrate para agendar citas, consultar tu historial
                y acceder a los servicios del consultorio.
            </p>
        </section>

        <!-- Formulario de registro -->
        <section class="flex items-center justify-center px-6 py-12">

            <div class="w-full max-w-xl">

                <header class="mb-8">
                    <p
                        class="text-sm font-semibold uppercase
                               tracking-[0.2em] text-cyan-700"
                    >
                        PsicoActúa
                    </p>

                    <h2 class="mt-3 text-3xl font-bold text-slate-900">
                        Crear cuenta
                    </h2>

                    <p class="mt-2 text-slate-600">
                        Completa tus datos personales para registrarte.
                    </p>
                </header>

                <form
                    action="/registro"
                    method="POST"
                    class="grid gap-5 md:grid-cols-2"
                >
                    <?php if ($errors !== []): ?>
                        <div
                            role="alert"
                            class="rounded-xl border border-red-200
                                bg-red-50 px-4 py-3 text-sm text-red-700
                                md:col-span-2"
                        >
                            <p class="font-semibold">
                                No fue posible completar el registro:
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                <?php foreach ($errors as $error): ?>
                                    <li>
                                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Nombres
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="given-name"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label
                            for="last_name"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Apellidos
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="family-name"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="email"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label
                            for="phone"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Número de teléfono
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="tel"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Contraseña
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Confirmar contraseña
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <label
                        class="flex items-start gap-3 text-sm text-slate-600
                               md:col-span-2"
                    >
                        <input
                            type="checkbox"
                            name="privacy_policy"
                            value="1"
                            required
                            class="mt-1 h-4 w-4 rounded border-slate-300"
                        >

                        <span>
                            He leído y acepto la política de privacidad
                            y protección de datos.
                        </span>
                    </label>

                    <button
                        type="submit"
                        class="rounded-xl bg-cyan-700 px-4 py-3
                               font-semibold text-white transition
                               hover:bg-cyan-800
                               focus:outline-none focus:ring-4
                               focus:ring-cyan-200 md:col-span-2"
                    >
                        Registrarme
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-600">
                    ¿Ya tienes una cuenta?

                    <a
                        href="/login"
                        class="font-semibold text-cyan-700
                               hover:text-cyan-900"
                    >
                        Inicia sesión
                    </a>
                </p>

            </div>
        </section>

    </main>

</body>
</html>
