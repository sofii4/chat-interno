<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 999px; }
        .card-anim { transition: transform .2s ease, border-color .2s ease, background-color .2s ease; }
        .card-anim:hover { transform: translateY(-2px); }
        body.theme-light {
            background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
            color: #0f172a;
        }
        body.theme-light .bg-gray-950 { background-color: #eef2ff !important; }
        body.theme-light .bg-gray-900,
        body.theme-light .bg-gray-900\/40,
        body.theme-light .bg-gray-800 { background-color: #ffffff !important; }
        body.theme-light .border-gray-800,
        body.theme-light .border-gray-700 { border-color: #cbd5e1 !important; }
        body.theme-light .text-white { color: #0f172a !important; }
        body.theme-light .text-gray-500,
        body.theme-light .text-gray-400 { color: #334155 !important; }
        body.theme-light .text-gray-300 { color: #1e293b !important; }
        body.theme-light .bg-indigo-600 { background-color: #6d28d9 !important; }
        body.theme-light .hover\:bg-indigo-500:hover { background-color: #7c3aed !important; }
        body.theme-light .focus\:ring-indigo-500:focus { --tw-ring-color: rgba(109, 40, 217, 0.35) !important; }
    </style>
</head>
<body class="bg-gray-950 text-white min-h-screen">
<?php $chamadosUsuario = $chamadosUsuario ?? []; ?>
<div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
    <div class="relative overflow-hidden rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-950 shadow-2xl">
        <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at top right, rgba(99,102,241,.35), transparent 40%), radial-gradient(circle at left bottom, rgba(34,197,94,.18), transparent 35%);"></div>
        <div class="relative p-6 md:p-8 flex flex-col gap-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="/chat" class="text-xs font-bold uppercase tracking-widest text-indigo-300 hover:text-indigo-200 transition">← Voltar ao chat</a>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-white">Meus Chamados</h1>
                    <p class="text-sm text-gray-400 mt-2 max-w-2xl">Acompanhe seus chamados abertos, em andamento e resolvidos.</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <button data-tab="abertos" class="tab-btn px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold">Abertos</button>
                    <button data-tab="resolvidos" class="tab-btn px-4 py-2 rounded-xl bg-gray-800 text-gray-300 text-sm font-bold border border-gray-700">Resolvidos</button>
                    <button data-tab="cancelados" class="tab-btn px-4 py-2 rounded-xl bg-gray-800 text-gray-300 text-sm font-bold border border-gray-700">Cancelados</button>
                    <button data-tab="todos" class="tab-btn px-4 py-2 rounded-xl bg-gray-800 text-gray-300 text-sm font-bold border border-gray-700">Todos</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-black/20 border border-gray-800 rounded-2xl p-4">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Total</p>
                    <p id="count-total" class="text-3xl font-black mt-2 text-white">0</p>
                </div>
                <div class="bg-black/20 border border-gray-800 rounded-2xl p-4">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Abertos</p>
                    <p id="count-abertos" class="text-3xl font-black mt-2 text-white">0</p>
                </div>
                <div class="bg-black/20 border border-gray-800 rounded-2xl p-4">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Resolvidos</p>
                    <p id="count-resolvidos" class="text-3xl font-black mt-2 text-white">0</p>
                </div>
                <div class="bg-black/20 border border-gray-800 rounded-2xl p-4">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Cancelados</p>
                    <p id="count-cancelados" class="text-3xl font-black mt-2 text-white">0</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-black/20 border border-gray-800 rounded-2xl p-3">
                <input id="busca-chamados" type="text" placeholder="Buscar por código, título, categoria ou status..." class="w-full bg-transparent text-sm text-white placeholder-gray-500 outline-none">
            </div>
        </div>
    </div>

    <section class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 id="titulo-lista" class="text-sm font-black uppercase tracking-[0.2em] text-gray-500">Chamados Abertos</h2>
            <span id="contador-lista" class="text-xs font-bold text-indigo-300"></span>
        </div>
        <div id="lista-chamados" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
    </section>
</div>

<div id="modal-detalhes" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm p-4 flex items-center justify-center">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-gray-900 border border-gray-800 rounded-3xl shadow-2xl">
        <div class="p-5 md:p-6 border-b border-gray-800 flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span id="detalhes-badge-id" class="text-[10px] font-black uppercase tracking-widest bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded"></span>
                    <span id="detalhes-status" class="text-[10px] font-black uppercase tracking-widest bg-gray-800 text-gray-300 px-2 py-0.5 rounded"></span>
                </div>
                <h3 id="detalhes-titulo" class="text-xl font-black text-white"></h3>
                <p id="detalhes-subtitulo" class="text-sm text-gray-400 mt-2"></p>
            </div>
            <div class="flex items-center gap-3">
                <button id="btn-cancelar-chamado" onclick="cancelarChamadoAtual()" class="hidden px-3 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-xs font-bold text-white border border-red-500 transition">Cancelar chamado</button>
                <button onclick="fecharModal('modal-detalhes')" class="text-gray-500 hover:text-white text-2xl leading-none">×</button>
            </div>
        </div>

        <div class="p-5 md:p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 text-sm">
                    <div class="bg-black/20 border border-gray-800 rounded-xl p-3"><p class="text-xs text-gray-500 uppercase font-bold">Categoria</p><p id="detalhes-categoria" class="text-white mt-1"></p></div>
                    <div class="bg-black/20 border border-gray-800 rounded-xl p-3"><p class="text-xs text-gray-500 uppercase font-bold">Subcategoria</p><p id="detalhes-subcategoria" class="text-white mt-1"></p></div>
                    <div class="bg-black/20 border border-gray-800 rounded-xl p-3"><p class="text-xs text-gray-500 uppercase font-bold">Abertura</p><p id="detalhes-criado" class="text-white mt-1"></p></div>
                    <div class="bg-black/20 border border-gray-800 rounded-xl p-3"><p class="text-xs text-gray-500 uppercase font-bold">Fechamento</p><p id="detalhes-fechado" class="text-white mt-1"></p></div>
                    <div class="bg-black/20 border border-gray-800 rounded-xl p-3 sm:col-span-2"><p class="text-xs text-gray-500 uppercase font-bold">Resolvido por</p><p id="detalhes-resolvido-por" class="text-white mt-1"></p></div>
                </div>
                <div class="bg-black/20 border border-gray-800 rounded-2xl p-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Descrição</p>
                    <div id="detalhes-descricao" class="text-sm text-gray-300 whitespace-pre-wrap leading-relaxed"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-500">Comentários</p>
                    <span class="text-xs text-gray-500">Somente leitura</span>
                </div>
                <div id="comentarios-lista" class="space-y-3 max-h-[60vh] overflow-y-auto pr-1"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.MEUS_CHAMADOS_BOOTSTRAP = <?= json_encode($chamadosUsuario ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="/assets/js/theme.js"></script>
<script src="/assets/js/meus-chamados.js"></script>
</body>
</html>
