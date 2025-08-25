<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Meu Projeto PHP'; ?></title>

    <base href="<?php echo BASE_URL; ?>">

    <script src="https://cdn.tailwindcss.com"></script>

    <?php if (!empty($pageScriptsHeader)): ?>
        <?php foreach ($pageScriptsHeader as $script): ?>
            <?php // A variável correta ($script) é usada aqui 
            ?>
            <script src="<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo $faviconImg ?? 'assets/img/favicon.png' ?>" sizes="32x32">

    <?php if (!empty($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style, ENT_QUOTES, 'UTF-8'); ?>">
        <?php endforeach; ?>
    <?php endif; ?>


    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* * CORREÇÃO: A regra .nav-link foi removida daqui, 
         * pois as classes de utilidade serão aplicadas diretamente no HTML.
         */
        /* .nav-link.active {
            @apply bg-indigo-100 text-indigo-700;
        } */
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <header class="bg-white shadow-sm mb-8">
        <nav class="container mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <a href="" class="text-xl font-bold text-indigo-600">MeuProjeto</a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="home" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Home</a>
                        <a href="sobre" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Sobre</a>
                        <a href="contato" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Contato</a>
                        <a href="agendamento" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Agendamento</a>
                        <a href="admin_ag" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Adm Agendamento</a>
                        <a href="users" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition-colors nav-link">Usuários</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 md:px-8">