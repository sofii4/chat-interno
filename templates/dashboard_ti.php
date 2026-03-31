<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard TI - Gestão de Chamados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
        .card-anim { transition: all 0.2s ease; }
        .card-anim:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-gray-950 text-white h-screen flex flex-col overflow-hidden">

    <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-8 shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" /></svg>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-none">Painel de Chamados</h1>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-indigo-400"><?= htmlspecialchars($userName) ?></p>
            </div>
            <a href="/chat" class="flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-xl text-sm font-medium transition border border-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Voltar ao Chat
            </a>
        </div>
    </header>

    <main class="flex-1 flex overflow-hidden p-6 gap-6">
        
        <section class="w-96 flex flex-col shrink-0">
            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    Aguardando Triagem
                </h3>
                <span id="count-triagem" class="bg-orange-500/10 text-orange-500 text-xs font-bold px-2.5 py-0.5 rounded-full border border-orange-500/20">0</span>
            </div>
            
            <div id="container-triagem" class="flex-1 overflow-y-auto space-y-4 pr-2">
                </div>
        </section>

        <section class="flex-1 flex flex-col bg-gray-900/40 rounded-3xl border border-gray-800/50 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest">Chamados Documentados</h3>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="abrirModalTaxonomias()" class="bg-gray-800 border border-gray-700 text-xs font-bold text-indigo-300 rounded-lg px-3 py-2 hover:bg-gray-700 transition">
                        GERENCIAR CATEGORIAS
                    </button>
                    <select id="filtro-setor" onchange="popularFiltroSubcategorias()" class="bg-gray-800 border border-gray-700 text-xs font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">TODOS OS SETORES</option>
                        <option value="ERP">ERP</option>
                        <option value="Infraestrutura">INFRAESTRUTURA</option>
                        <option value="Engenharia">ENGENHARIA</option>
                        <option value="Redes">REDES</option>
                        <option value="Segurança">SEGURANÇA</option>
                        <option value="Hardware">HARDWARE</option>
                        <option value="Acessos">ACESSOS</option>
                    </select>
                    <select id="filtro-subcategoria" onchange="renderizarTudo()" class="bg-gray-800 border border-gray-700 text-xs font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">TODAS AS SUBCATEGORIAS</option>
                    </select>
                    <select id="filtro-ordenacao" onchange="renderizarTudo()" class="bg-gray-800 border border-gray-700 text-xs font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">MAIS RECENTES</option>
                        <option value="antigos">MAIS ANTIGOS</option>
                    </select>
                </div>
            </div>

            <div id="container-documentados" class="flex-1 overflow-y-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 content-start pr-2">
                </div>
        </section>

        <aside id="painel-historico" class="w-80 flex flex-col shrink-0 bg-gray-900/40 rounded-3xl border border-gray-800/50 transition-all duration-300">
            <div class="p-4 border-b border-gray-800/70 flex items-center gap-3">
                <div id="historico-header-info" class="flex-1 min-w-0 flex items-center justify-between">
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest">Histórico</h3>
                    <span id="count-finalizados" class="bg-green-500/10 text-green-500 text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-500/20">0</span>
                </div>
                <button id="btn-toggle-historico" onclick="togglePainelHistorico()" class="w-8 h-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 flex items-center justify-center transition" title="Minimizar histórico">
                    <svg id="icone-historico" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </div>

            <div id="filtros-historico" class="px-4 pt-3 pb-2 border-b border-gray-800/70 space-y-2">
                <select id="filtro-historico-categoria" onchange="popularFiltroHistoricoSubcategorias()" class="w-full bg-gray-800 border border-gray-700 text-[11px] font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">CATEGORIA (TODAS)</option>
                </select>
                <select id="filtro-historico-subcategoria" onchange="renderizarTudo()" class="w-full bg-gray-800 border border-gray-700 text-[11px] font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">SUBCATEGORIA (TODAS)</option>
                </select>
                <input id="filtro-historico-data" type="date" onchange="renderizarTudo()" class="w-full bg-gray-800 border border-gray-700 text-[11px] font-bold text-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <div id="conteudo-historico" class="flex-1 overflow-y-auto p-4 space-y-3">
            </div>
        </aside>
    </main>

    <div id="modal-classificar" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 bg-gray-800/50 border-b border-gray-800 shrink-0">
                <div class="flex items-center gap-3 mb-1">
                    <span id="classificar-id-badge" class="bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded text-xs font-mono font-bold"></span>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Aguardando Triagem</span>
                </div>
                <h3 id="classificar-titulo" class="text-xl font-bold text-white"></h3>
            </div>
            
            <div class="p-6 overflow-y-auto">
                
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Descrição do Problema</label>
                    <div id="classificar-descricao" class="text-sm text-gray-300 bg-black/30 p-4 rounded-xl border border-gray-800/50 max-h-56 overflow-y-auto"></div>
                </div>

                <div id="classificar-anexo-container" class="mb-6 hidden">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Arquivos Anexados</label>
                    <div id="classificar-anexos-lista" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1"></div>
                </div>

                <form id="form-classificar" class="space-y-5 border-t border-gray-800 pt-6">
                    <input type="hidden" id="classificar-id-input">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Definir Prioridade</label>
                        <select id="sel-prioridade" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Categoria</label>
                            <select id="sel-categoria" onchange="atualizarSubcategorias()" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Subcategoria</label>
                            <select id="sel-subcategoria" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Aguardando...</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="fecharModal('modal-classificar')" class="flex-1 px-4 py-3 bg-gray-800 text-gray-400 font-bold rounded-xl hover:bg-gray-700 transition">Cancelar</button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-500 transition">Confirmar Triagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-detalhes" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-4xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button onclick="fecharModal('modal-detalhes')" class="absolute top-4 right-4 text-gray-500 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span id="detalhes-id-badge" class="bg-gray-800 text-gray-400 px-2 py-0.5 rounded text-xs font-mono font-bold"></span>
                        <span id="detalhes-prioridade" class="text-[10px] font-black px-2 py-0.5 rounded uppercase"></span>
                    </div>
                    <h3 id="detalhes-titulo" class="text-xl font-bold text-white mb-4"></h3>

                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1 tracking-widest">Descrição</label>
                    <div id="detalhes-descricao" class="text-sm text-gray-300 bg-black/30 p-4 rounded-xl border border-gray-800/50 mb-4 max-h-56 overflow-y-auto"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                        <p id="detalhes-meta-categoria" class="text-xs text-gray-400"></p>
                        <p id="detalhes-meta-subcategoria" class="text-xs text-gray-400"></p>
                        <p id="detalhes-meta-data-abertura" class="text-xs text-gray-400"></p>
                        <p id="detalhes-meta-data-fechamento" class="text-xs text-gray-400"></p>
                        <p id="detalhes-meta-solicitante" class="text-xs text-gray-400 sm:col-span-2"></p>
                    </div>

                    <p id="detalhes-resolvido-por" class="text-xs text-gray-500"></p>
                </div>

                <div id="detalhes-anexo-container" class="hidden">
                    <div id="detalhes-anexos-lista" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-1"></div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-800 flex gap-3 mt-6">
                <button id="detalhes-btn-editar" class="flex-1 bg-gray-800 hover:bg-amber-600 text-gray-300 hover:text-white text-xs font-bold py-2.5 rounded-lg transition">EDITAR CLASSIFICAÇÃO</button>
                <button id="detalhes-btn-chamar" class="flex-1 bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white text-xs font-bold py-2.5 rounded-lg transition">CHAMAR SETOR</button>
                <button id="detalhes-btn-finalizar" class="flex-1 bg-gray-800 hover:bg-green-600 text-gray-300 hover:text-white text-xs font-bold py-2.5 rounded-lg transition">FINALIZAR</button>
            </div>
        </div>
    </div>

    <div id="modal-taxonomias" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-2xl shadow-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Categorias e Subcategorias</h3>
                <button onclick="fecharModal('modal-taxonomias')" class="text-gray-500 hover:text-white">✕</button>
            </div>

            <form id="form-taxonomia" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <input id="taxonomia-categoria" placeholder="Categoria" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                <input id="taxonomia-subcategoria" placeholder="Subcategoria" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 rounded-lg text-sm font-bold">Adicionar</button>
            </form>

            <div id="lista-taxonomias" class="max-h-80 overflow-y-auto space-y-2"></div>
        </div>
    </div>
    <script src="/assets/js/dashboard-ti.js"></script>
</body>
</html>