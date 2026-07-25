<section class="flex flex-col justify-center items-center h-[80vh]">
    <h1 class="text-6xl font-bold text-sky-700 mb-4">
        Psico Actúa 🚀
    </h1>
    <p class="text-xl text-slate-600">
        Plataforma profesional de atención psicológica
    </p>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="mt-8 text-center">
            <p class="mb-4 text-lg text-slate-700">
                Bienvenido,
                <?= htmlspecialchars(
                    $_SESSION['user']['name'],
                    ENT_QUOTES,
                    'UTF-8' ) ?>
            </p>
            <form action="/logout" method="POST">
                <button
                    type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2
                        font-semibold text-white transition
                        hover:bg-red-700"
                >
                    Cerrar sesión
                </button>
            </form>
        </div>
    <?php endif; ?>
</section>
