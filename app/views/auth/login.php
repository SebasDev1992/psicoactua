<?php

/**
 * Vista pública de inicio de sesión.
 *
 * Por ahora contiene únicamente la interfaz.
 * En el siguiente paso conectaremos el formulario
 * con el controlador y la base de datos.
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

    <title>Iniciar sesión | PsicoActúa</title>

    <link
        rel="stylesheet"
        href="/assets/css/app.css"
    >
</head>

<body class="min-h-screen bg-slate-100">

    <main class="min-h-screen grid lg:grid-cols-2">

        <!-- Sección informativa -->
        <section
            class="hidden lg:flex flex-col justify-center px-16
                   bg-slate-900 text-white"
        >
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">
                Psicoagenda
            </p>

            <h1 class="mt-4 text-4xl font-bold leading-tight">
                Bienvenido a PsicoActúa
            </h1>

            <p class="mt-6 max-w-lg text-lg text-slate-300">
                Gestiona tus citas, consulta tu historial y accede
                a los servicios del consultorio de manera segura.
            </p>
        </section>

        <!-- Formulario -->
        <section
            class="flex items-center justify-center px-6 py-12"
        >
            <div class="w-full max-w-md">

                <header class="mb-8">
                    <p
                        class="text-sm font-semibold uppercase
                               tracking-[0.2em] text-cyan-700"
                    >
                        PsicoActúa
                    </p>

                    <h2 class="mt-3 text-3xl font-bold text-slate-900">
                        Iniciar sesión
                    </h2>

                    <p class="mt-2 text-slate-600">
                        Ingresa tus datos para acceder a la plataforma.
                    </p>
                </header>

                <form
                    action="/login"
                    method="POST"
                    class="space-y-5"
                >
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
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-slate-900
                                   outline-none transition
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
                            autocomplete="current-password"
                            placeholder="Ingresa tu contraseña"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-slate-900
                                   outline-none transition
                                   focus:border-cyan-600
                                   focus:ring-4 focus:ring-cyan-100"
                        >
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input
                                type="checkbox"
                                name="remember"
                                class="h-4 w-4 rounded border-slate-300"
                            >

                            Recordarme
                        </label>

                        <a
                            href="/recuperar-contrasena"
                            class="text-sm font-medium text-cyan-700
                                   hover:text-cyan-900"
                        >
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-cyan-700 px-4 py-3
                               font-semibold text-white transition
                               hover:bg-cyan-800
                               focus:outline-none focus:ring-4
                               focus:ring-cyan-200"
                    >
                        Iniciar sesión
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-600">
                    ¿Aún no tienes una cuenta?

                    <a
                        href="/registro"
                        class="font-semibold text-cyan-700
                               hover:text-cyan-900"
                    >
                        Regístrate
                    </a>
                </p>

            </div>
        </section>

    </main>

</body>
</html>
