<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Chat Interno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/light-mode.css">
    <style>
    </style>
</head>
<body class="page-login bg-gray-950 min-h-screen flex items-center justify-center p-4">

<button data-theme-toggle class="fixed top-6 right-6 z-20 w-9 h-9 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 flex items-center justify-center transition" title="Alternar tema">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1m15.02 6.36l-.7-.7M6.34 6.34l-.7-.7m12.02 0l-.7.7M6.34 17.66l-.7.7M12 8a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>
</button>

<div class="w-full max-w-sm">

    <!-- Logo / título -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-600 rounded-2xl mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Chat Interno</h1>
        <p class="text-gray-400 text-sm mt-1">Entre com suas credenciais</p>
    </div>

    <!-- Card do formulário -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl">

        <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-5">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">E-mail</label>
                <input type="email" id="email" name="email" required autofocus
                       placeholder="seu@empresa.com"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-300 mb-2">Senha</label>
                <input type="password" id="senha" name="senha" required
                       placeholder="••••••••"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl py-3 text-sm transition-colors duration-200 mt-2">
                Entrar
            </button>

        </form>
    </div>

    <p class="text-center text-gray-600 text-xs mt-6">Chat Interno &copy; <?= date('Y') ?></p>
</div>

<script src="/assets/js/theme.js"></script>
</body>
</html>
