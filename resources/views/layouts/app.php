<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Psico Actúa' ?></title>

    <link rel="stylesheet" href="/assets/css/app.css?v=1">
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main class="flex-1">
        <?= $content ?? '' ?>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>

</body>

</html>