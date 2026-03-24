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
                    <div id="classificar-descricao" class="text-sm text-gray-300 bg-black/30 p-4 rounded-xl border border-gray-800/50"></div>
                </div>

                <div id="classificar-anexo-container" class="mb-6 hidden">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-widest">Arquivos Anexados</label>
                    <img id="classificar-anexo-preview" class="hidden w-full max-h-64 object-contain bg-black/30 border border-gray-800/50 rounded-xl p-2 mb-3" alt="Prévia do anexo">
                    <div class="flex flex-wrap gap-2">
                        <a id="classificar-anexo-view-btn" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-sm text-white font-bold rounded-lg transition">
                            Visualizar
                        </a>
                        <a id="classificar-anexo-btn" href="#" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-sm text-indigo-400 font-bold rounded-lg border border-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Baixar Arquivo
                        </a>
                    </div>
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
        <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-lg shadow-2xl p-6 relative">
            <button onclick="fecharModal('modal-detalhes')" class="absolute top-4 right-4 text-gray-500 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <div class="flex items-center gap-3 mb-2">
                <span id="detalhes-id-badge" class="bg-gray-800 text-gray-400 px-2 py-0.5 rounded text-xs font-mono font-bold"></span>
                <span id="detalhes-prioridade" class="text-[10px] font-black px-2 py-0.5 rounded uppercase"></span>
            </div>
            <h3 id="detalhes-titulo" class="text-xl font-bold text-white mb-4"></h3>
            
            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1 tracking-widest">Descrição</label>
            <div id="detalhes-descricao" class="text-sm text-gray-300 bg-black/30 p-4 rounded-xl border border-gray-800/50 mb-4"></div>

            <div id="detalhes-anexo-container" class="mb-4 hidden">
                <img id="detalhes-anexo-preview" class="hidden w-full max-h-72 object-contain bg-black/30 border border-gray-800/50 rounded-xl p-2 mb-3" alt="Prévia do anexo">
                <div class="flex flex-wrap gap-2">
                    <a id="detalhes-anexo-view-btn" href="#" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-xs text-white font-bold rounded-lg transition">
                        Visualizar
                    </a>
                    <a id="detalhes-anexo-btn" href="#" target="_blank" download class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-xs text-indigo-400 font-bold rounded-lg border border-gray-700 transition">
                        Baixar Anexo
                    </a>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-800 flex gap-3">
                <button id="detalhes-btn-chamar" class="flex-1 bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white text-xs font-bold py-2.5 rounded-lg transition">CHAMAR SETOR</button>
                <button id="detalhes-btn-finalizar" class="flex-1 bg-gray-800 hover:bg-green-600 text-gray-300 hover:text-white text-xs font-bold py-2.5 rounded-lg transition">FINALIZAR</button>
            </div>
        </div>
    </div>

    <script>
        const CONFIG = {
            categorias: {
                "ERP": ["Financeiro", "Fiscal", "Contabilidade", "Vendas", "Estoque"],
                "Engenharia": ["AutoCAD", "Solidworks", "Revisão Técnica"],
                "Infraestrutura": ["Servidor", "Backup", "Cloud", "Banco de Dados"],
                "Redes": ["Wi-Fi", "Cabeamento", "VPN"],
                "Segurança": ["Antivírus", "Firewall", "Câmeras"],
                "Hardware": ["Desktop/Notebook", "Impressora", "Periféricos"],
                "Acessos": ["Reset de Senha", "Novo Usuário", "Permissões"]
            },
            prioridades: {
                "critica": { label: "CRÍTICA", color: "bg-red-500", border: "border-red-500" },
                "alta":    { label: "ALTA",    color: "bg-orange-500", border: "border-orange-500" },
                "media":   { label: "MÉDIA",   color: "bg-yellow-500", border: "border-yellow-500" },
                "baixa":   { label: "BAIXA",   color: "bg-blue-500", border: "border-blue-500" }
            }
        };

        let chamadosCache = [];
        let historicoMinimizado = false;

        // INICIALIZAÇÃO
        document.addEventListener('DOMContentLoaded', () => {
            popularCategorias();
            popularFiltroSubcategorias();
            carregarDados();

            // Adicionando a função que envia a classificação e atualiza a tela
            const formClassificar = document.getElementById('form-classificar');
            if (formClassificar) {
                formClassificar.onsubmit = async (e) => {
                    e.preventDefault();
                    const btn = document.querySelector('#form-classificar button[type="submit"]');
                    const textoOriginal = btn.innerText;
                    btn.innerText = "Salvando...";
                    btn.disabled = true;

                    const id = document.getElementById('classificar-id-input').value;
                    const data = {
                        prioridade: document.getElementById('sel-prioridade').value,
                        categoria: document.getElementById('sel-categoria').value,
                        subcategoria: document.getElementById('sel-subcategoria').value
                    };
                    
                    try {
                        const res = await fetch(`/api/chamados/${id}/classificar`, {
                            method: 'PATCH',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(data)
                        });

                        if (res.ok) { 
                            fecharModal('modal-classificar'); 
                            await carregarDados(); // Recarrega os cards (agora vai para documentados)
                        } else {
                            alert("Erro ao classificar o chamado no servidor.");
                        }
                    } catch (err) {
                        console.error("Erro no Javascript:", err);
                        alert("Erro ao processar a requisição.");
                    } finally {
                        btn.innerText = textoOriginal;
                        btn.disabled = false;
                    }
                };
            }
        });

        function popularCategorias() {
            const sel = document.getElementById('sel-categoria');
            Object.keys(CONFIG.categorias).forEach(cat => {
                sel.innerHTML += `<option value="${cat}">${cat}</option>`;
            });
        }

        function atualizarSubcategorias() {
            const cat = document.getElementById('sel-categoria').value;
            const selSub = document.getElementById('sel-subcategoria');
            selSub.innerHTML = '<option value="">Selecione...</option>';
            if(cat) CONFIG.categorias[cat].forEach(s => selSub.innerHTML += `<option value="${s}">${s}</option>`);
        }

        function popularFiltroSubcategorias() {
            const filtroSetor = document.getElementById('filtro-setor');
            const filtroSub = document.getElementById('filtro-subcategoria');
            if (!filtroSub) return;

            const categoriaSelecionada = filtroSetor ? filtroSetor.value : '';
            const valorAtual = filtroSub.value;

            filtroSub.innerHTML = '<option value="">TODAS AS SUBCATEGORIAS</option>';

            if (categoriaSelecionada && CONFIG.categorias[categoriaSelecionada]) {
                CONFIG.categorias[categoriaSelecionada].forEach(sub => {
                    filtroSub.innerHTML += `<option value="${sub}">${sub.toUpperCase()}</option>`;
                });
                filtroSub.disabled = false;
            } else {
                filtroSub.disabled = true;
            }

            if (!filtroSub.disabled && valorAtual) {
                filtroSub.value = valorAtual;
            } else {
                filtroSub.value = '';
            }

            renderizarTudo();
        }

        async function carregarDados() {
            const res = await fetch('/api/chamados');
            chamadosCache = await res.json();
            renderizarTudo();
        }

        function renderizarTudo() {
            const filtroEl = document.getElementById('filtro-setor');
            const filtro = filtroEl ? filtroEl.value : '';
            const filtroSubEl = document.getElementById('filtro-subcategoria');
            const filtroSub = filtroSubEl ? filtroSubEl.value : '';
            const filtroOrdenacaoEl = document.getElementById('filtro-ordenacao');
            const filtroOrdenacao = filtroOrdenacaoEl ? filtroOrdenacaoEl.value : '';
            const triagem = document.getElementById('container-triagem');
            const doc = document.getElementById('container-documentados');
            const historico = document.getElementById('conteudo-historico');
            
            triagem.innerHTML = '';
            doc.innerHTML = '';
            if (historico) historico.innerHTML = '';

            // Ordenação (Crítica primeiro)
            const ordem = { critica: 1, alta: 2, media: 3, baixa: 4 };
            chamadosCache.sort((a,b) => ordem[a.prioridade] - ordem[b.prioridade]);

            const pendentes = chamadosCache.filter(c => c.status === 'aberto');
            pendentes.forEach(c => triagem.innerHTML += cardTriagem(c));
            
            const countTriagem = document.getElementById('count-triagem');
            if(countTriagem) countTriagem.innerText = pendentes.length;

            let documentados = chamadosCache.filter(c => {
                if (c.status !== 'classificado') return false;

                const matchCategoria = !filtro || filtro === 'Todos' || c.categoria === filtro;
                const matchSubcategoria = !filtroSub || c.subcategoria === filtroSub;

                return matchCategoria && matchSubcategoria;
            });

            // Aplicar ordenação: se não há filtro de data, ordena por urgência
            if (!filtroOrdenacao) {
                // Por padrão: ordenar por urgência (prioridade)
                documentados.sort((a, b) => ordem[a.prioridade] - ordem[b.prioridade]);
            } else if (filtroOrdenacao === 'antigos') {
                // Mais antigos (ordem crescente)
                documentados.sort((a, b) => new Date(a.criado_em) - new Date(b.criado_em));
            } else {
                // Mais recentes (ordem decrescente)
                documentados.sort((a, b) => new Date(b.criado_em) - new Date(a.criado_em));
            }

            documentados.forEach(c => doc.innerHTML += cardDocumentado(c));

            const finalizados = chamadosCache.filter(c => c.status === 'resolvido');
            const countFinalizados = document.getElementById('count-finalizados');
            if (countFinalizados) countFinalizados.innerText = finalizados.length;

            if (historico) {
                if (finalizados.length) {
                    finalizados.forEach(c => historico.innerHTML += cardHistorico(c));
                } else {
                    historico.innerHTML = '<p class="text-xs text-gray-600">Nenhum chamado finalizado ainda.</p>';
                }
            }
        }

        function cardTriagem(c) {
            const nome = c.usuario_nome || 'Usuário';
            const inicial = nome.charAt(0).toUpperCase();

            return `
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl card-anim group">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gray-800 rounded-lg flex items-center justify-center text-[10px] font-bold text-indigo-500 border border-gray-700">${inicial}</div>
                        <span class="text-[10px] text-gray-400 font-bold">${nome}</span>
                    </div>
                    <span class="text-[10px] text-gray-600 font-bold">${new Date(c.criado_em).toLocaleDateString()}</span>
                </div>
                <h4 class="text-white font-bold text-sm mb-2 group-hover:text-indigo-400 transition-colors">#${c.id} - ${c.titulo}</h4>
                <p class="text-gray-500 text-xs line-clamp-2 mb-4 leading-relaxed">${c.descricao_rich}</p>
                <button onclick="abrirModalClassificar(${c.id})" class="w-full py-2.5 bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white text-xs font-black rounded-xl transition-all">CLASSIFICAR</button>
            </div>`;
        }

        function cardDocumentado(c) {
            const p = CONFIG.prioridades[c.prioridade] || CONFIG.prioridades['media'];
            const nome = c.usuario_nome || 'Usuário';
            const inicial = nome.charAt(0).toUpperCase();
            const tituloEscapado = JSON.stringify(c.titulo || '');

            return `
            <div onclick="abrirModalDetalhes(${c.id})" class="bg-gray-900 border-l-4 ${p.border} p-5 rounded-r-2xl shadow-xl card-anim cursor-pointer hover:bg-gray-800/80 transition flex flex-col h-full relative group">
                <div class="flex justify-between items-start mb-3">
                    <span class="${p.color} text-[9px] font-black text-black px-2 py-0.5 rounded uppercase">${p.label}</span>
                    <span class="text-[10px] text-indigo-400 font-bold uppercase">${c.categoria}</span>
                </div>
                <h4 class="text-white font-bold text-sm mb-1">#${c.id} - ${c.titulo}</h4>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-[10px] text-gray-500 font-medium">${c.subcategoria || 'Sem subcategoria'}</p>
                    <span class="text-[10px] text-gray-500 font-bold">${new Date(c.criado_em).toLocaleDateString()}</span>
                </div>
                
                <div class="flex items-center gap-2 pt-3 border-t border-gray-800 mt-auto mb-4">
                    <div class="w-6 h-6 bg-gray-800 rounded-lg flex items-center justify-center text-[10px] font-bold text-indigo-500 border border-gray-700">${inicial}</div>
                    <span class="text-[10px] text-gray-400">${nome}</span>
                </div>

                <div class="flex gap-2" onclick="event.stopPropagation()">
                    <button onclick="chamarSetor(${c.usuario_id})" class="flex-1 bg-gray-800 hover:bg-indigo-600 text-gray-300 hover:text-white text-[10px] font-bold py-2 rounded-lg transition">CHAMAR SETOR</button>
                    <button onclick='finalizarChamado(${c.id}, ${tituloEscapado})' class="flex-1 bg-gray-800 hover:bg-green-600 text-gray-300 hover:text-white text-[10px] font-bold py-2 rounded-lg transition">FINALIZAR</button>
                </div>
            </div>`;
        }

        function cardHistorico(c) {
            const nome = c.usuario_nome || 'Usuário';
            return `
            <button onclick="abrirModalDetalhes(${c.id})" class="w-full text-left bg-gray-900 border border-gray-800 hover:border-green-500/30 p-3 rounded-xl transition">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="text-[10px] font-black uppercase text-green-500">Finalizado</span>
                    <span class="text-[10px] text-gray-500">${new Date(c.atualizado_em || c.criado_em).toLocaleDateString()}</span>
                </div>
                <p class="text-xs font-bold text-white truncate">#${c.id} - ${c.titulo}</p>
                <p class="text-[10px] text-gray-500 mt-1 truncate">Solicitante: ${nome}</p>
            </button>`;
        }

        function togglePainelHistorico() {
            historicoMinimizado = !historicoMinimizado;

            const painel = document.getElementById('painel-historico');
            const headerInfo = document.getElementById('historico-header-info');
            const conteudo = document.getElementById('conteudo-historico');
            const icone = document.getElementById('icone-historico');
            const botao = document.getElementById('btn-toggle-historico');

            if (!painel || !headerInfo || !conteudo || !icone || !botao) return;

            if (historicoMinimizado) {
                painel.classList.remove('w-80');
                painel.classList.add('w-16');
                headerInfo.classList.add('hidden');
                conteudo.classList.add('hidden');
                icone.classList.add('rotate-180');
                botao.title = 'Expandir histórico';
            } else {
                painel.classList.remove('w-16');
                painel.classList.add('w-80');
                headerInfo.classList.remove('hidden');
                conteudo.classList.remove('hidden');
                icone.classList.remove('rotate-180');
                botao.title = 'Minimizar histórico';
            }
        }

        function obterAnexoChamado(chamado) {
            if (chamado?.anexo_path) {
                return {
                    path: chamado.anexo_path,
                    nome: chamado.anexo_nome || 'anexo',
                    mime: chamado.anexo_mime || ''
                };
            }

            if (chamado?.anexo || chamado?.arquivo) {
                const path = chamado.anexo || chamado.arquivo;
                return { path, nome: path.split('/').pop() || 'anexo', mime: '' };
            }

            return null;
        }

        function getAnexoUrl(path) {
            return `/uploads/${path}`;
        }

        function isAnexoImagem(anexo) {
            if (!anexo) return false;
            if (anexo.mime && anexo.mime.startsWith('image/')) return true;
            return /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(anexo.nome || anexo.path || '');
        }

        function abrirModalClassificar(id) {
            const c = chamadosCache.find(x => x.id === id); 
            if (!c) return;
            
            document.getElementById('classificar-id-input').value = c.id;
            document.getElementById('classificar-id-badge').innerText = "#" + c.id;
            document.getElementById('classificar-titulo').innerText = c.titulo;
            document.getElementById('classificar-descricao').innerHTML = c.descricao_rich;
            
            const anexo = obterAnexoChamado(c);
            const anexoContainer = document.getElementById('classificar-anexo-container');
            const anexoPreview = document.getElementById('classificar-anexo-preview');
            const anexoViewBtn = document.getElementById('classificar-anexo-view-btn');
            const anexoDownBtn = document.getElementById('classificar-anexo-btn');

            if (anexo && anexoContainer && anexoViewBtn && anexoDownBtn && anexoPreview) {
                const anexoUrl = getAnexoUrl(anexo.path);
                anexoViewBtn.href = anexoUrl;
                anexoDownBtn.href = anexoUrl;
                anexoDownBtn.setAttribute('download', anexo.nome || 'anexo');

                if (isAnexoImagem(anexo)) {
                    anexoPreview.src = anexoUrl;
                    anexoPreview.classList.remove('hidden');
                } else {
                    anexoPreview.removeAttribute('src');
                    anexoPreview.classList.add('hidden');
                }

                anexoContainer.classList.remove('hidden');
            } else {
                if (anexoPreview) {
                    anexoPreview.removeAttribute('src');
                    anexoPreview.classList.add('hidden');
                }
                if (anexoContainer) anexoContainer.classList.add('hidden');
            }

            document.getElementById('modal-classificar').classList.remove('hidden');
        }

        function abrirModalDetalhes(id) {
            const c = chamadosCache.find(x => x.id === id);
            if (!c) return;
            const p = CONFIG.prioridades[c.prioridade] || CONFIG.prioridades['media'];
            const tituloEscapado = c.titulo || '';

            document.getElementById('detalhes-id-badge').innerText = "#" + c.id;
            document.getElementById('detalhes-titulo').innerText = c.titulo;
            document.getElementById('detalhes-descricao').innerHTML = c.descricao_rich;
            
            const badgePrioridade = document.getElementById('detalhes-prioridade');
            badgePrioridade.className = `${p.color} text-[10px] font-black text-black px-2 py-0.5 rounded uppercase`;
            badgePrioridade.innerText = p.label;

            const anexo = obterAnexoChamado(c);
            const anexoContainer = document.getElementById('detalhes-anexo-container');
            const anexoPreview = document.getElementById('detalhes-anexo-preview');
            const anexoViewBtn = document.getElementById('detalhes-anexo-view-btn');
            const anexoDownBtn = document.getElementById('detalhes-anexo-btn');

            if (anexo && anexoContainer && anexoViewBtn && anexoDownBtn && anexoPreview) {
                const anexoUrl = getAnexoUrl(anexo.path);
                anexoViewBtn.href = anexoUrl;
                anexoDownBtn.href = anexoUrl;
                anexoDownBtn.setAttribute('download', anexo.nome || 'anexo');

                if (isAnexoImagem(anexo)) {
                    anexoPreview.src = anexoUrl;
                    anexoPreview.classList.remove('hidden');
                } else {
                    anexoPreview.removeAttribute('src');
                    anexoPreview.classList.add('hidden');
                }

                anexoContainer.classList.remove('hidden');
            } else {
                if (anexoPreview) {
                    anexoPreview.removeAttribute('src');
                    anexoPreview.classList.add('hidden');
                }
                if (anexoContainer) anexoContainer.classList.add('hidden');
            }

            const btnChamar = document.getElementById('detalhes-btn-chamar');
            const btnFinalizar = document.getElementById('detalhes-btn-finalizar');

            if (btnChamar) {
                btnChamar.onclick = () => chamarSetor(c.usuario_id);
            }

            if (btnFinalizar) {
                if (c.status === 'resolvido') {
                    btnFinalizar.disabled = true;
                    btnFinalizar.innerText = 'JÁ FINALIZADO';
                    btnFinalizar.classList.remove('hover:bg-green-600');
                    btnFinalizar.classList.add('opacity-60', 'cursor-not-allowed');
                    btnFinalizar.onclick = null;
                } else {
                    btnFinalizar.disabled = false;
                    btnFinalizar.innerText = 'FINALIZAR';
                    btnFinalizar.classList.add('hover:bg-green-600');
                    btnFinalizar.classList.remove('opacity-60', 'cursor-not-allowed');
                    btnFinalizar.onclick = () => finalizarChamado(c.id, tituloEscapado);
                }
            }

            document.getElementById('modal-detalhes').classList.remove('hidden');
        }

        function fecharModal(modalId) { 
            document.getElementById(modalId).classList.add('hidden'); 
        }

        function chamarSetor(usuarioId) {
            window.location.href = `/chat?conversa_com=${usuarioId}`; 
        }

        async function finalizarChamado(id, titulo) {
            // 1. Confirma com o TI se ele quer mesmo finalizar
            if(!confirm(`Tem certeza que deseja finalizar o chamado #${id} - ${titulo}?`)) return;
            
            try {
                // 2. Envia a ordem para o servidor (PHP) via API
                const res = await fetch(`/api/chamados/${id}/finalizar`, { method: 'PATCH' });
                
                if(res.ok) {
                    // 3. Deu tudo certo! O PHP finalizou e guardou a mensagem no banco.
                    alert(`Chamado finalizado com sucesso! A mensagem automática foi enviada no chat do usuário.`);
                    
                    // 4. Recarrega a tela para o card sumir (ou ir para resolvidos)
                    await carregarDados();
                } else {
                    alert("Erro ao finalizar chamado no servidor. Verifique os logs do PHP.");
                }
            } catch(e) {
                console.error("Erro de comunicação do JS com a API:", e);
                alert("Erro de conexão ao tentar finalizar o chamado.");
            }
        }
    </script>
</body>
</html>