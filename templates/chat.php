<!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chat Interno</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            #messages {
                scroll-behavior: smooth;
            }

            .msg-enter {
                animation: fadeUp .2s ease;
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            ::-webkit-scrollbar {
                width: 4px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: #374151;
                border-radius: 999px;
            }
        </style>
    </head>

    <body class="bg-gray-950 text-white h-screen flex overflow-hidden">

        <aside class="w-72 bg-gray-900 border-r border-gray-800 flex flex-col shrink-0">
            <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-sm font-bold">
                        <?= strtoupper(substr($userName, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">
                            <?= htmlspecialchars($userName) ?>
                        </p>
                        <p class="text-xs text-green-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
                            Online
                        </p>
                    </div>
                </div>
                <a href="/logout" title="Sair"
                    class="text-gray-500 hover:text-red-400 transition p-1.5 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>

            <div class="p-3 border-b border-gray-800">
                <button onclick="abrirEmergencia()"
                    class="w-full bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-xl py-2.5 px-4 flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    Chamar TI — Emergência
                </button>
            </div>

            <div class="p-3">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Buscar..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-9 pr-4 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-2 pb-4 space-y-0.5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 pt-3 pb-2">Grupos</p>
                <div id="lista-conversas" class="space-y-0.5"></div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 pt-4 pb-2">Usuários Online
                </p>
                <div id="lista-usuarios" class="space-y-0.5"></div>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-700 rounded-lg flex items-center justify-center text-sm">#</div>
                    <div>
                        <p id="chat-nome" class="font-semibold text-white text-sm">Selecione uma conversa</p>
                        <p class="text-xs text-gray-400">Chat Interno</p>
                    </div>
                </div>
            </header>

            <div id="messages" class="flex-1 overflow-y-auto p-6 space-y-4">
                <p class="text-center text-gray-600 text-xs py-8">Selecione uma conversa para começar</p>
            </div>

            <div id="typing-indicator" class="hidden px-6 py-1 text-xs text-gray-500 italic"></div>
    <div id="typing-indicator" class="hidden px-6 py-1 text-xs text-gray-500 italic"></div>
    <div class="p-4 bg-gray-900 border-t border-gray-800 shrink-0">
                <div
                    class="flex items-end gap-3 bg-gray-800 border border-gray-700 rounded-2xl px-4 py-3 focus-within:ring-2 focus-within:ring-indigo-500 transition">
                    <button class="text-gray-400 hover:text-indigo-400 transition shrink-0 mb-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <textarea id="msg-input" rows="1" placeholder="Selecione uma conversa..."
                        class="flex-1 bg-transparent text-sm text-white placeholder-gray-500 resize-none focus:outline-none max-h-32"
                        onkeydown="handleEnter(event)" oninput="autoResize(this)"></textarea>
                    <button onclick="enviarMensagem()"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl p-2 transition shrink-0 mb-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-2 ml-1">Enter para enviar · Shift+Enter para nova linha</p>
            </div>
        </main>

        <!-- MODAL EMERGÊNCIA -->
        <div id="modal-emergencia"
            class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gray-900 border border-red-500/30 rounded-2xl w-full max-w-2xl shadow-2xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-white">Chamado de Emergência — TI</h2>
                            <p class="text-xs text-gray-400">A equipe será notificada imediatamente</p>
                        </div>
                    </div>
                    <button onclick="fecharEmergencia()" class="text-gray-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Título do problema</label>
                        <input type="text" id="chamado-titulo" placeholder="Ex: Impressora do 2º andar não funciona"
                            class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Prioridade</label>
                        <div class="flex gap-2">
                            <button
                                class="prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border border-gray-700 text-gray-400 transition"
                                data-valor="baixa">Baixa</button>
                            <button
                                class="prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border border-yellow-500 text-yellow-400 transition"
                                data-valor="media">Média</button>
                            <button
                                class="prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border border-gray-700 text-gray-400 transition"
                                data-valor="alta">Alta</button>
                            <button
                                class="prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border border-gray-700 text-gray-400 transition"
                                data-valor="critica">🔴 Crítica</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Descrição detalhada</label>
                        <textarea id="chamado-descricao" rows="4"
                            placeholder="Descreva o problema: o que aconteceu, quando começou, quais equipamentos afetados..."
                            class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Anexar arquivos</label>
                        <label
                            class="flex items-center gap-3 bg-gray-800 border border-dashed border-gray-600 rounded-xl px-4 py-3 cursor-pointer hover:border-gray-500 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-sm text-gray-400">Clique para selecionar arquivos</span>
                            <input type="file" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 px-6 pb-6">
                    <button onclick="fecharEmergencia()"
                        class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl py-2.5 text-sm font-medium transition">
                        Cancelar
                    </button>
                    <button id="btn-enviar-chamado" onclick="enviarChamado()" class="flex-1 bg-red-600 hover:bg-red-500 text-white rounded-xl py-2.5 text-sm font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Abrir Chamado de Emergência
                    </button>
                </div>
            </div>
        </div>

        <script>
const CURRENT_USER_ID   = <?= (int) $userId ?>;
const CURRENT_USER_NAME = "<?= addslashes(htmlspecialchars($userName)) ?>";
let conversaAtualId   = null;
let conversaAtualNome = null;
let ws                = null;
let typingTimer       = null;

// ── WebSocket ─────────────────────────────────
function conectarWS() {
    const host = window.location.hostname;
    ws = new WebSocket(`ws://${host}:8080`);

    ws.onopen = () => {
        console.log('WebSocket conectado!');
        // Autentica imediatamente ao conectar
        ws.send(JSON.stringify({
            type:       'auth',
            user_id:    CURRENT_USER_ID,
            user_nome:  CURRENT_USER_NAME,
            conversa_id: conversaAtualId ?? 0,
        }));
    };

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);

        switch (data.type) {
            case 'auth_ok':
                console.log('Autenticado no WS como userId:', data.userId);
                break;

            case 'new_message':
                // Só renderiza se for da conversa atual
                if (data.message.conversa_id == conversaAtualId ||
                    data.message.usuario_id  == CURRENT_USER_ID) {
                    renderizarMensagem(data.message);
                    document.getElementById('messages').scrollTop = 99999;
                }
                atualizarPreviewSidebar(data.message);
                break;

            case 'typing':
                if (data.conversa_id == conversaAtualId) {
                    mostrarTyping(data.user_nome);
                }
                break;
        }
    };

    ws.onclose = () => {
        console.log('WS desconectado. Reconectando em 3s...');
        setTimeout(conectarWS, 3000); // reconexão automática
    };

    ws.onerror = (err) => {
        console.error('WS erro:', err);
    };
}

// ── Inicialização ─────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    conectarWS();
    carregarConversas();
    carregarUsuarios();

    document.querySelectorAll('.prioridade-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            prioridadeSelecionada = btn.dataset.valor;
            document.querySelectorAll('.prioridade-btn').forEach(b => {
                b.className = 'prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border border-gray-700 text-gray-400 transition';
            });
            const cores = {
                baixa:  'border-green-500 text-green-400',
                media:  'border-yellow-500 text-yellow-400',
                alta:   'border-orange-500 text-orange-400',
                critica:'border-red-500 text-red-400'
            };
            btn.className = `prioridade-btn px-4 py-1.5 rounded-lg text-xs font-medium border ${cores[btn.dataset.valor]} transition`;
        });
    });

    document.getElementById('modal-emergencia').addEventListener('click', function(e) {
        if (e.target === this) fecharEmergencia();
    });
});

// ── Conversas (sidebar) ───────────────────────
async function carregarConversas() {
    const res   = await fetch('/api/conversas');
    const lista = await res.json();
    const nav   = document.getElementById('lista-conversas');
    nav.innerHTML = '';

    lista.forEach(c => {
        const btn = document.createElement('button');
        btn.className = 'conversa-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800 transition text-left';
        btn.dataset.id = c.id;
        btn.dataset.nome = c.nome;
        btn.innerHTML = `
            <div class="w-9 h-9 bg-indigo-700 rounded-xl flex items-center justify-center shrink-0 text-sm">#</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${c.nome}</p>
                <p class="preview-msg text-xs text-gray-400 truncate">${c.ultima_mensagem ?? 'Sem mensagens'}</p>
            </div>`;
        btn.addEventListener('click', () => selecionarConversa(c.id, c.nome, btn));
        nav.appendChild(btn);
    });

    if (lista.length > 0) {
        const primeiro = nav.querySelector('.conversa-item');
        selecionarConversa(lista[0].id, lista[0].nome, primeiro);
    }
}

function atualizarPreviewSidebar(msg) {
    const btn = document.querySelector(`.conversa-item[data-id="${msg.conversa_id ?? conversaAtualId}"]`);
    if (btn) {
        const preview = btn.querySelector('.preview-msg');
        if (preview) preview.textContent = msg.conteudo.substring(0, 40);
    }
}

// ── Usuários (sidebar) ────────────────────────
async function carregarUsuarios() {
    const res   = await fetch('/api/usuarios/online');
    const lista = await res.json();
    const nav   = document.getElementById('lista-usuarios');
    nav.innerHTML = '';

    if (lista.length === 0) {
        nav.innerHTML = '<p class="text-xs text-gray-600 px-3 py-2">Nenhum outro usuário cadastrado</p>';
        return;
    }

    const cores = ['bg-pink-700','bg-emerald-700','bg-amber-700','bg-purple-700'];
    lista.forEach(u => {
        const cor = cores[u.id % cores.length];
        const btn = document.createElement('button');
        btn.className = 'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800 transition text-left';
        btn.innerHTML = `
            <div class="w-9 h-9 ${cor} rounded-xl flex items-center justify-center text-sm font-bold shrink-0">
                ${u.nome.charAt(0).toUpperCase()}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${u.nome}</p>
                <p class="text-xs text-gray-400 truncate">${u.setor ?? u.papel}</p>
            </div>`;
        nav.appendChild(btn);
    });
}

// ── Selecionar conversa ───────────────────────
function selecionarConversa(id, nome, el) {
    conversaAtualId   = id;
    conversaAtualNome = nome;

    document.querySelectorAll('.conversa-item').forEach(b => b.classList.remove('bg-gray-800'));
    el.classList.add('bg-gray-800');

    document.getElementById('chat-nome').textContent = '#' + nome;
    document.getElementById('msg-input').placeholder = `Mensagem para #${nome}...`;
    document.getElementById('typing-indicator').classList.add('hidden');

    // Avisa o servidor WS que mudou de sala
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ type: 'join', conversa_id: id }));
    }

    carregarMensagens(id);
}

// ── Histórico via HTTP (não WebSocket) ────────
async function carregarMensagens(conversaId) {
    const box = document.getElementById('messages');
    box.innerHTML = '<p class="text-center text-gray-600 text-xs py-4">Carregando...</p>';

    const res  = await fetch(`/api/mensagens?conversa_id=${conversaId}`);
    const msgs = await res.json();

    box.innerHTML = '';

    if (msgs.length === 0) {
        box.innerHTML = '<p class="text-center text-gray-600 text-xs py-8">Nenhuma mensagem ainda. Diga olá! 👋</p>';
        return;
    }

    msgs.forEach(m => renderizarMensagem(m));
    box.scrollTop = box.scrollHeight;
}

// ── Renderizar mensagem ───────────────────────
function renderizarMensagem(m) {
    const box    = document.getElementById('messages');
    const proprio = m.usuario_id === CURRENT_USER_ID;
    const inicial = m.usuario_nome.charAt(0).toUpperCase();
    const cores  = ['bg-emerald-700','bg-pink-700','bg-amber-700','bg-purple-700'];
    const cor    = proprio ? 'bg-indigo-600' : cores[m.usuario_id % cores.length];
    const hora   = new Date(m.criado_em).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    const texto  = m.conteudo
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\n/g,'<br>');

    const div = document.createElement('div');
    div.className = `flex items-start gap-3 msg-enter ${proprio ? 'flex-row-reverse' : ''}`;
    div.innerHTML = `
        <div class="w-8 h-8 ${cor} rounded-lg flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">${inicial}</div>
        <div class="max-w-lg">
            <div class="flex items-baseline gap-2 mb-1 ${proprio ? 'flex-row-reverse' : ''}">
                <span class="text-sm font-semibold ${proprio ? 'text-indigo-400' : 'text-white'}">${proprio ? 'Você' : m.usuario_nome}</span>
                <span class="text-xs text-gray-500">${hora}</span>
            </div>
            <div class="${proprio ? 'bg-indigo-600' : 'bg-gray-800'} rounded-2xl ${proprio ? 'rounded-tr-sm' : 'rounded-tl-sm'} px-4 py-2.5 text-sm ${proprio ? 'text-white' : 'text-gray-200'}">
                ${texto}
            </div>
        </div>`;

    // Remove mensagem de "sem mensagens" se existir
    const vazio = box.querySelector('p.text-center');
    if (vazio) vazio.remove();

    box.appendChild(div);
}

// ── Enviar mensagem via WebSocket ─────────────
function enviarMensagem() {
    if (!conversaAtualId) return;
    const input = document.getElementById('msg-input');
    const texto = input.value.trim();
    if (!texto) return;

    if (ws && ws.readyState === WebSocket.OPEN) {
        // Envia via WebSocket — o servidor salva no banco e faz broadcast
        ws.send(JSON.stringify({
            type:        'send_message',
            conversa_id: conversaAtualId,
            conteudo:    texto,
        }));
    } else {
        // Fallback HTTP se WS estiver desconectado
        fetch('/api/mensagens', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    `conversa_id=${conversaAtualId}&conteudo=${encodeURIComponent(texto)}`
        }).then(r => r.json()).then(m => {
            renderizarMensagem(m);
            document.getElementById('messages').scrollTop = 99999;
        });
    }

    input.value = '';
    input.style.height = 'auto';
}

// ── Indicador "digitando..." ──────────────────
function mostrarTyping(nome) {
    const el = document.getElementById('typing-indicator');
    el.textContent = `${nome} está digitando...`;
    el.classList.remove('hidden');
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => el.classList.add('hidden'), 2000);
}

function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarMensagem(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 128) + 'px';

    // Avisa outros usuários que está digitando
    if (ws && ws.readyState === WebSocket.OPEN && conversaAtualId) {
        ws.send(JSON.stringify({ type: 'typing', conversa_id: conversaAtualId }));
    }
}

// ── Emergência ────────────────────────────────
let prioridadeSelecionada = 'media';
function abrirEmergencia()  { document.getElementById('modal-emergencia').classList.remove('hidden'); }
function fecharEmergencia() { document.getElementById('modal-emergencia').classList.add('hidden'); }

async function enviarChamado() {
    const titulo    = document.getElementById('chamado-titulo').value.trim();
    const descricao = document.getElementById('chamado-descricao').value.trim();
    const fileInput = document.querySelector('#modal-emergencia input[type=file]');

    if (!titulo) { alert('Informe o título do problema.'); return; }

    const btnEnviar = document.getElementById('btn-enviar-chamado');
    btnEnviar.disabled = true;
    btnEnviar.textContent = 'Enviando...';

    try {
        const formData = new FormData();
        formData.append('titulo',     titulo);
        formData.append('descricao',  descricao);
        formData.append('prioridade', prioridadeSelecionada);

        if (fileInput && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(f => formData.append('anexos[]', f));
        }

        const res  = await fetch('/api/chamados', { method: 'POST', body: formData });
        const data = await res.json();

        if (!res.ok) throw new Error(data.erro ?? 'Erro ao abrir chamado');

        // Limpa o formulário
        document.getElementById('chamado-titulo').value = '';
        document.getElementById('chamado-descricao').value = '';
        if (fileInput) fileInput.value = '';

        fecharEmergencia();
        mostrarSucesso(data.chamado);

    } catch (err) {
        alert('Erro: ' + err.message);
    } finally {
        btnEnviar.disabled = false;
        btnEnviar.textContent = 'Abrir Chamado de Emergência';
    }
}

function mostrarSucesso(chamado) {
    const prioridades = { baixa:'🟢', media:'🟡', alta:'🟠', critica:'🔴' };
    const icone = prioridades[chamado.prioridade] ?? '🔴';

    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 bg-gray-800 border border-green-500/30 text-white rounded-2xl p-4 shadow-2xl z-50 max-w-sm';
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center shrink-0 text-sm">✓</div>
            <div>
                <p class="font-semibold text-green-400 text-sm">Chamado aberto!</p>
                <p class="text-gray-300 text-xs mt-0.5">${icone} ${chamado.titulo}</p>
                <p class="text-gray-500 text-xs mt-1">A equipe de TI foi notificada. #${chamado.id}</p>
            </div>
        </div>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 8000);
}
</script>

</body>
</html>