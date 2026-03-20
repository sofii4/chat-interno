<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #messages { scroll-behavior: smooth; }
        .msg-enter { animation: fadeUp .2s ease; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 999px; }
    </style>
</head>
<body class="bg-gray-950 text-white h-screen flex overflow-hidden">

<!-- ═══ SIDEBAR ═══ -->
<aside class="w-72 bg-gray-900 border-r border-gray-800 flex flex-col shrink-0">

    <div class="p-4 border-b border-gray-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-sm font-bold">
                <?= strtoupper(substr($userName, 0, 1)) ?>
            </div>
            <div>
                <p class="text-sm font-semibold text-white"><?= htmlspecialchars($userName) ?></p>
                <p class="text-xs text-green-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
                    Online
                </p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <?php if ($userPapel === 'admin'): ?>
            <a href="/admin" title="Painel Admin"
               class="text-gray-500 hover:text-indigo-400 transition p-1.5 rounded-lg hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
            <?php endif; ?>
            <a href="/logout" title="Sair"
               class="text-gray-500 hover:text-red-400 transition p-1.5 rounded-lg hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="p-3 border-b border-gray-800">
        <button onclick="abrirEmergencia()"
                class="w-full bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-xl py-2.5 px-4 flex items-center justify-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Chamar TI — Emergência
        </button>
    </div>

    <div class="p-3 flex items-center gap-2">
        <div class="relative flex-1">
            <svg class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Buscar..."
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-9 pr-4 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button onclick="abrirModalNovaConversa()" title="Nova conversa"
                class="w-9 h-9 bg-gray-800 border border-gray-700 hover:border-indigo-500 hover:text-indigo-400 text-gray-400 rounded-xl flex items-center justify-center transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 pb-4 space-y-0.5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 pt-3 pb-2">Conversas</p>
        <div id="lista-conversas" class="space-y-0.5"></div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 pt-4 pb-2">Usuários Online</p>
        <div id="lista-usuarios" class="space-y-0.5"></div>
    </nav>
</aside>

<!-- ═══ ÁREA PRINCIPAL ═══ -->
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

    <div class="p-4 bg-gray-900 border-t border-gray-800 shrink-0">
        <div class="flex items-end gap-3 bg-gray-800 border border-gray-700 rounded-2xl px-4 py-3 focus-within:ring-2 focus-within:ring-indigo-500 transition">
            <button class="text-gray-400 hover:text-indigo-400 transition shrink-0 mb-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </button>
            <textarea id="msg-input" rows="1"
                      placeholder="Selecione uma conversa..."
                      class="flex-1 bg-transparent text-sm text-white placeholder-gray-500 resize-none focus:outline-none max-h-32"
                      onkeydown="handleEnter(event)"
                      oninput="autoResize(this)"></textarea>
            <button onclick="enviarMensagem()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl p-2 transition shrink-0 mb-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-600 mt-2 ml-1">Enter para enviar · Shift+Enter para nova linha</p>
    </div>
</main>

<!-- ═══ MODAL EMERGÊNCIA ═══ -->
<div id="modal-emergencia" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-red-500/30 rounded-2xl w-full max-w-2xl shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-white">Chamado de Emergência — TI</h2>
                    <p class="text-xs text-gray-400">A equipe será notificada imediatamente</p>
                </div>
            </div>
            <button onclick="fecharEmergencia()" class="text-gray-500 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                <label class="block text-sm font-medium text-gray-300 mb-2">Descrição detalhada</label>
                <textarea id="chamado-descricao" rows="4"
                          placeholder="Descreva o problema: o que aconteceu, quando começou, quais equipamentos afetados..."
                          class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Anexar arquivos</label>
                <label class="flex items-center gap-3 bg-gray-800 border border-dashed border-gray-600 rounded-xl px-4 py-3 cursor-pointer hover:border-gray-500 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
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
            <button id="btn-enviar-chamado" onclick="enviarChamado()"
                    class="flex-1 bg-red-600 hover:bg-red-500 text-white rounded-xl py-2.5 text-sm font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
                Abrir Chamado de Emergência
            </button>
        </div>
    </div>
</div>

<!-- ═══ MODAL NOVA CONVERSA ═══ -->
<div id="modal-nova-conversa" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <h3 class="font-bold text-white">Nova Conversa</h3>
            <button onclick="fecharModalNovaConversa()" class="text-gray-500 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex bg-gray-800 rounded-xl p-1 gap-1">
                <button id="tab-privada" onclick="trocarTipoConversa('privada')"
                        class="flex-1 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white transition">
                    Conversa Privada
                </button>
                <?php if ($userPapel === 'admin'): ?>
                <button id="tab-grupo" onclick="trocarTipoConversa('grupo')"
                        class="flex-1 py-2 text-sm font-medium rounded-lg text-gray-400 hover:text-white transition">
                    Criar Grupo
                </button>
                <?php endif; ?>
            </div>
            <div id="form-privada">
                <label class="block text-sm font-medium text-gray-300 mb-2">Selecione o usuário</label>
                <div id="lista-usuarios-conversa" class="space-y-1 max-h-64 overflow-y-auto"></div>
            </div>
            <div id="form-grupo" class="hidden space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nome do grupo</label>
                    <input type="text" id="grupo-nome" placeholder="Ex: Projeto X"
                           class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Participantes iniciais</label>
                    <div id="lista-usuarios-grupo" class="space-y-1 max-h-48 overflow-y-auto"></div>
                </div>
                <button onclick="criarGrupo()"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl py-2.5 text-sm font-bold transition">
                    Criar Grupo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL EDITAR GRUPO ═══ -->
<div id="modal-editar-grupo" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <div>
                <h3 class="font-bold text-white">Editar Grupo</h3>
                <p id="editar-grupo-subtitulo" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="fecharModalEditarGrupo()" class="text-gray-500 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <!-- Renomear -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nome do grupo</label>
                <div class="flex gap-2">
                    <input type="text" id="editar-grupo-nome"
                           class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button onclick="salvarNomeGrupo()"
                            class="bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl px-4 py-2.5 text-sm font-medium transition">
                        Salvar
                    </button>
                </div>
            </div>
            <!-- Membros atuais -->
            <div>
                <p class="text-sm font-medium text-gray-300 mb-3">Membros atuais</p>
                <div id="editar-grupo-membros" class="space-y-1 max-h-40 overflow-y-auto"></div>
            </div>
            <!-- Adicionar membros -->
            <div class="border-t border-gray-800 pt-4">
                <p class="text-sm font-medium text-gray-300 mb-3">Adicionar membros</p>
                <div id="editar-grupo-disponiveis" class="space-y-1 max-h-40 overflow-y-auto"></div>
            </div>
            <!-- Excluir grupo -->
            <div class="border-t border-gray-800 pt-4">
                <button onclick="confirmarExcluirGrupo()"
                        class="w-full bg-red-600/10 hover:bg-red-600/20 border border-red-500/30 text-red-400 rounded-xl py-2.5 text-sm font-medium transition">
                    Excluir grupo permanentemente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const CURRENT_USER_ID   = <?= (int) $userId ?>;
const CURRENT_USER_NAME = "<?= addslashes(htmlspecialchars($userName)) ?>";
const IS_ADMIN          = <?= $userPapel === 'admin' ? 'true' : 'false' ?>;
let conversaAtualId   = null;
let conversaAtualNome = null;
let ws                = null;
let typingTimer       = null;
let grupoEditandoId   = null;
let grupoEditandoNome = null;

// ── WebSocket ─────────────────────────────────
function conectarWS() {
    const host = window.location.hostname;
    ws = new WebSocket('ws://' + host + ':8080');

    ws.onopen = function() {
        console.log('WebSocket conectado!');
        ws.send(JSON.stringify({
            type:        'auth',
            user_id:     CURRENT_USER_ID,
            user_nome:   CURRENT_USER_NAME,
            conversa_id: conversaAtualId || 0,
        }));
    };

    ws.onmessage = function(event) {
        const data = JSON.parse(event.data);
        switch (data.type) {
            case 'auth_ok':
                console.log('Autenticado no WS como userId:', data.userId);
                break;
            case 'new_message':
                if (data.message.conversa_id == conversaAtualId) {
                    renderizarMensagem(data.message);
                    document.getElementById('messages').scrollTop = 99999;
                    fetch('/api/conversas/' + conversaAtualId + '/lida', { method: 'POST' });
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

    ws.onclose = function() {
        console.log('WS desconectado. Reconectando em 3s...');
        setTimeout(conectarWS, 3000);
    };

    ws.onerror = function(err) {
        console.error('WS erro:', err);
    };
}

// ── Inicialização ─────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    conectarWS();
    carregarConversas();
    carregarUsuarios();
    document.getElementById('modal-emergencia').addEventListener('click', function(e) {
        if (e.target === this) fecharEmergencia();
    });
    document.getElementById('modal-nova-conversa').addEventListener('click', function(e) {
        if (e.target === this) fecharModalNovaConversa();
    });
    document.getElementById('modal-editar-grupo').addEventListener('click', function(e) {
        if (e.target === this) fecharModalEditarGrupo();
    });
});

// ── Conversas (sidebar) ───────────────────────
async function carregarConversas() {
    const res   = await fetch('/api/conversas');
    const lista = await res.json();
    const nav   = document.getElementById('lista-conversas');
    nav.innerHTML = '';

    lista.forEach(function(c) {
        const isGrupo = c.tipo === 'grupo' || c.tipo === 'setor';
        const icone   = isGrupo ? '#' : c.nome.charAt(0).toUpperCase();
        const cor     = isGrupo ? 'bg-indigo-700' : 'bg-emerald-700';
        const badge   = c.nao_lidas > 0 ? c.nao_lidas : '';
        const badgeHidden = c.nao_lidas > 0 ? '' : 'hidden';

        const wrapper = document.createElement('div');
        wrapper.className = 'group relative';

        let editBtn = '';
        if (isGrupo && IS_ADMIN) {
            editBtn = '<button onclick="abrirModalEditarGrupo(' + c.id + ', \'' + c.nome.replace(/'/g, "\\'") + '\')" '
                    + 'class="absolute right-2 top-1/2 -translate-y-1/2 hidden group-hover:flex w-6 h-6 items-center justify-center text-gray-500 hover:text-indigo-400 transition rounded-lg hover:bg-indigo-500/10">'
                    + '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                    + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                    + '</svg></button>';
        }

        wrapper.innerHTML = '<button class="conversa-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800 transition text-left" data-id="' + c.id + '" data-nome="' + c.nome + '">'
            + '<div class="w-9 h-9 ' + cor + ' rounded-xl flex items-center justify-center shrink-0 text-sm font-bold">' + icone + '</div>'
            + '<div class="flex-1 min-w-0">'
            + '<p class="text-sm font-medium text-white truncate">' + c.nome + '</p>'
            + '<p class="preview-msg text-xs text-gray-400 truncate">' + (c.ultima_mensagem || 'Sem mensagens') + '</p>'
            + '</div>'
            + '<span class="badge-nao-lidas ' + badgeHidden + ' bg-indigo-600 text-white text-xs rounded-full min-w-5 h-5 flex items-center justify-center px-1 shrink-0">' + badge + '</span>'
            + '</button>'
            + editBtn;

        const btn = wrapper.querySelector('.conversa-item');
        btn.addEventListener('click', function() { selecionarConversa(c.id, c.nome, btn); });
        nav.appendChild(wrapper);
    });

    if (lista.length > 0) {
        const primeiro = nav.querySelector('.conversa-item');
        selecionarConversa(lista[0].id, lista[0].nome, primeiro);
    }
}

function atualizarPreviewSidebar(msg) {
    const cId = msg.conversa_id || conversaAtualId;
    const btn = document.querySelector('.conversa-item[data-id="' + cId + '"]');
    if (!btn) return;

    const preview = btn.querySelector('.preview-msg');
    if (preview) preview.textContent = msg.conteudo.substring(0, 40);

    if (cId != conversaAtualId) {
        const badge = btn.querySelector('.badge-nao-lidas');
        if (badge) {
            const atual = parseInt(badge.textContent) || 0;
            badge.textContent = atual + 1;
            badge.classList.remove('hidden');
        }
    }
}

function limparBadge(conversaId) {
    const btn = document.querySelector('.conversa-item[data-id="' + conversaId + '"]');
    if (!btn) return;
    const badge = btn.querySelector('.badge-nao-lidas');
    if (badge) { badge.textContent = ''; badge.classList.add('hidden'); }
}

// ── Usuários (sidebar) ────────────────────────
async function carregarUsuarios() {
    const res   = await fetch('/api/usuarios/online');
    const lista = await res.json();
    const nav   = document.getElementById('lista-usuarios');
    nav.innerHTML = '';

    if (!lista.length) {
        nav.innerHTML = '<p class="text-xs text-gray-600 px-3 py-2">Nenhum outro usuário cadastrado</p>';
        return;
    }

    const cores = ['bg-pink-700', 'bg-emerald-700', 'bg-amber-700', 'bg-purple-700'];
    lista.forEach(function(u) {
        const cor = cores[u.id % cores.length];
        const btn = document.createElement('button');
        btn.className = 'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800 transition text-left';
        btn.innerHTML = '<div class="w-9 h-9 ' + cor + ' rounded-xl flex items-center justify-center text-sm font-bold shrink-0">' + u.nome.charAt(0).toUpperCase() + '</div>'
            + '<div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">' + u.nome + '</p>'
            + '<p class="text-xs text-gray-400 truncate">' + (u.setor || u.papel) + '</p></div>';
        nav.appendChild(btn);
    });
}

// ── Selecionar conversa ───────────────────────
function selecionarConversa(id, nome, el) {
    conversaAtualId   = id;
    conversaAtualNome = nome;
    document.querySelectorAll('.conversa-item').forEach(function(b) { b.classList.remove('bg-gray-800'); });
    limparBadge(id);
    el.classList.add('bg-gray-800');
    document.getElementById('chat-nome').textContent = '#' + nome;
    document.getElementById('msg-input').placeholder = 'Mensagem para #' + nome + '...';
    document.getElementById('typing-indicator').classList.add('hidden');
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ type: 'join', conversa_id: id }));
    }
    fetch('/api/conversas/' + id + '/lida', { method: 'POST' });
    carregarMensagens(id);
}

// ── Histórico ─────────────────────────────────
async function carregarMensagens(conversaId) {
    const box = document.getElementById('messages');
    box.innerHTML = '<p class="text-center text-gray-600 text-xs py-4">Carregando...</p>';
    const res  = await fetch('/api/mensagens?conversa_id=' + conversaId);
    const msgs = await res.json();
    box.innerHTML = '';
    if (!msgs.length) {
        box.innerHTML = '<p class="text-center text-gray-600 text-xs py-8">Nenhuma mensagem ainda. Diga olá! 👋</p>';
        return;
    }
    msgs.forEach(function(m) { renderizarMensagem(m); });
    box.scrollTop = box.scrollHeight;
}

// ── Renderizar mensagem ───────────────────────
function renderizarMensagem(m) {
    const box    = document.getElementById('messages');
    const proprio = m.usuario_id === CURRENT_USER_ID;
    const inicial = m.usuario_nome.charAt(0).toUpperCase();
    const cores  = ['bg-emerald-700', 'bg-pink-700', 'bg-amber-700', 'bg-purple-700'];
    const cor    = proprio ? 'bg-indigo-600' : cores[m.usuario_id % cores.length];
    const hora   = new Date(m.criado_em).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    const texto  = m.conteudo.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');

    const div = document.createElement('div');
    div.className = 'flex items-start gap-3 msg-enter' + (proprio ? ' flex-row-reverse' : '');
    div.innerHTML = '<div class="w-8 h-8 ' + cor + ' rounded-lg flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">' + inicial + '</div>'
        + '<div class="max-w-lg">'
        + '<div class="flex items-baseline gap-2 mb-1' + (proprio ? ' flex-row-reverse' : '') + '">'
        + '<span class="text-sm font-semibold ' + (proprio ? 'text-indigo-400' : 'text-white') + '">' + (proprio ? 'Você' : m.usuario_nome) + '</span>'
        + '<span class="text-xs text-gray-500">' + hora + '</span>'
        + '</div>'
        + '<div class="' + (proprio ? 'bg-indigo-600' : 'bg-gray-800') + ' rounded-2xl ' + (proprio ? 'rounded-tr-sm' : 'rounded-tl-sm') + ' px-4 py-2.5 text-sm ' + (proprio ? 'text-white' : 'text-gray-200') + '">' + texto + '</div>'
        + '</div>';

    const vazio = box.querySelector('p.text-center');
    if (vazio) vazio.remove();
    box.appendChild(div);
}

// ── Enviar mensagem ───────────────────────────
function enviarMensagem() {
    if (!conversaAtualId) return;
    const input = document.getElementById('msg-input');
    const texto = input.value.trim();
    if (!texto) return;

    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({ type: 'send_message', conversa_id: conversaAtualId, conteudo: texto }));
    } else {
        fetch('/api/mensagens', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'conversa_id=' + conversaAtualId + '&conteudo=' + encodeURIComponent(texto)
        }).then(function(r) { return r.json(); }).then(function(m) {
            renderizarMensagem(m);
            document.getElementById('messages').scrollTop = 99999;
        });
    }
    input.value = '';
    input.style.height = 'auto';
}

function mostrarTyping(nome) {
    const el = document.getElementById('typing-indicator');
    el.textContent = nome + ' está digitando...';
    el.classList.remove('hidden');
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function() { el.classList.add('hidden'); }, 2000);
}

function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarMensagem(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 128) + 'px';
    if (ws && ws.readyState === WebSocket.OPEN && conversaAtualId) {
        ws.send(JSON.stringify({ type: 'typing', conversa_id: conversaAtualId }));
    }
}

// ── Emergência ────────────────────────────────
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
        formData.append('titulo', titulo);
        formData.append('descricao', descricao);
        if (fileInput && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(function(f) { formData.append('anexos[]', f); });
        }

        const res  = await fetch('/api/chamados', { method: 'POST', body: formData });
        const data = await res.json();
        if (!res.ok) throw new Error(data.erro || 'Erro ao abrir chamado');

        document.getElementById('chamado-titulo').value = '';
        document.getElementById('chamado-descricao').value = '';
        if (fileInput) fileInput.value = '';
        fecharEmergencia();

        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 bg-gray-800 border border-green-500/30 text-white rounded-2xl p-4 shadow-2xl z-50 max-w-sm';
        toast.innerHTML = '<div class="flex items-start gap-3">'
            + '<div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center shrink-0 text-sm">✓</div>'
            + '<div><p class="font-semibold text-green-400 text-sm">Chamado aberto!</p>'
            + '<p class="text-gray-300 text-xs mt-0.5">' + data.chamado.titulo + '</p>'
            + '<p class="text-gray-500 text-xs mt-1">A equipe de TI foi notificada. #' + data.chamado.id + '</p></div></div>';
        document.body.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 8000);

    } catch (err) {
        alert('Erro: ' + err.message);
    } finally {
        btnEnviar.disabled = false;
        btnEnviar.textContent = 'Abrir Chamado de Emergência';
    }
}

// ── Nova Conversa ─────────────────────────────
let tipoConversa = 'privada';
let usuariosSelecionados = new Set();

function abrirModalNovaConversa() {
    tipoConversa = 'privada';
    usuariosSelecionados.clear();
    document.getElementById('form-privada').classList.remove('hidden');
    document.getElementById('form-grupo').classList.add('hidden');
    document.getElementById('modal-nova-conversa').classList.remove('hidden');
    carregarUsuariosModal();
}

function fecharModalNovaConversa() {
    document.getElementById('modal-nova-conversa').classList.add('hidden');
}

function trocarTipoConversa(tipo) {
    tipoConversa = tipo;
    document.getElementById('form-privada').classList.toggle('hidden', tipo !== 'privada');
    document.getElementById('form-grupo').classList.toggle('hidden', tipo !== 'grupo');
    document.getElementById('tab-privada').className = 'flex-1 py-2 text-sm font-medium rounded-lg transition ' + (tipo === 'privada' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white');
    const tabGrupo = document.getElementById('tab-grupo');
    if (tabGrupo) tabGrupo.className = 'flex-1 py-2 text-sm font-medium rounded-lg transition ' + (tipo === 'grupo' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white');
    if (tipo === 'grupo') carregarUsuariosGrupo();
}

async function carregarUsuariosModal() {
    const res   = await fetch('/api/usuarios/online');
    const lista = await res.json();
    const div   = document.getElementById('lista-usuarios-conversa');
    if (!lista.length) {
        div.innerHTML = '<p class="text-xs text-gray-500 py-2">Nenhum outro usuário cadastrado.</p>';
        return;
    }
    const cores = ['bg-pink-700', 'bg-emerald-700', 'bg-amber-700', 'bg-purple-700', 'bg-blue-700'];
    div.innerHTML = lista.map(function(u) {
        return '<button onclick="iniciarConversaPrivada(' + u.id + ', \'' + u.nome.replace(/'/g, "\\'") + '\')" '
            + 'class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800 transition text-left">'
            + '<div class="w-8 h-8 ' + cores[u.id % cores.length] + ' rounded-lg flex items-center justify-center text-xs font-bold shrink-0">' + u.nome.charAt(0).toUpperCase() + '</div>'
            + '<div><p class="text-sm font-medium text-white">' + u.nome + '</p>'
            + '<p class="text-xs text-gray-400">' + (u.setor || u.papel) + '</p></div>'
            + '</button>';
    }).join('');
}

async function carregarUsuariosGrupo() {
    const res   = await fetch('/api/usuarios/online');
    const lista = await res.json();
    const div   = document.getElementById('lista-usuarios-grupo');
    const cores = ['bg-pink-700', 'bg-emerald-700', 'bg-amber-700', 'bg-purple-700', 'bg-blue-700'];
    div.innerHTML = lista.map(function(u) {
        return '<label class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-800 transition cursor-pointer">'
            + '<input type="checkbox" value="' + u.id + '" class="rounded" onchange="toggleParticipante(' + u.id + ')">'
            + '<div class="w-7 h-7 ' + cores[u.id % cores.length] + ' rounded-lg flex items-center justify-center text-xs font-bold shrink-0">' + u.nome.charAt(0).toUpperCase() + '</div>'
            + '<span class="text-sm text-white">' + u.nome + '</span></label>';
    }).join('');
}

function toggleParticipante(id) {
    if (usuariosSelecionados.has(id)) usuariosSelecionados.delete(id);
    else usuariosSelecionados.add(id);
}

async function iniciarConversaPrivada(usuarioId, usuarioNome) {
    const res  = await fetch('/api/conversas', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'tipo=privada&usuario_id=' + usuarioId,
    });
    const data = await res.json();
    if (!res.ok) { alert(data.erro); return; }
    fecharModalNovaConversa();
    await carregarConversas();
    const btn = document.querySelector('.conversa-item[data-id="' + data.id + '"]');
    if (btn) selecionarConversa(data.id, usuarioNome, btn);
}

async function criarGrupo() {
    const nome = document.getElementById('grupo-nome').value.trim();
    if (!nome) { alert('Informe o nome do grupo.'); return; }
    const participantes = Array.from(usuariosSelecionados).join(',');
    const res  = await fetch('/api/conversas', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'tipo=grupo&nome=' + encodeURIComponent(nome) + '&participantes=' + participantes,
    });
    const data = await res.json();
    if (!res.ok) { alert(data.erro); return; }
    fecharModalNovaConversa();
    await carregarConversas();
    const btn = document.querySelector('.conversa-item[data-id="' + data.id + '"]');
    if (btn) selecionarConversa(data.id, nome, btn);
}

// ── Editar Grupo ──────────────────────────────
function abrirModalEditarGrupo(id, nome) {
    grupoEditandoId   = id;
    grupoEditandoNome = nome;
    document.getElementById('editar-grupo-nome').value = nome;
    document.getElementById('editar-grupo-subtitulo').textContent = nome;
    document.getElementById('modal-editar-grupo').classList.remove('hidden');
    carregarMembrosGrupo();
}

function fecharModalEditarGrupo() {
    document.getElementById('modal-editar-grupo').classList.add('hidden');
    grupoEditandoId   = null;
    grupoEditandoNome = null;
}

async function carregarMembrosGrupo() {
    const resMembros = await fetch('/api/conversas/' + grupoEditandoId + '/participantes');
    const membros    = await resMembros.json();
    const resAll     = await fetch('/api/usuarios/online');
    const todos      = await resAll.json();

    const divMembros = document.getElementById('editar-grupo-membros');
    const divDisp    = document.getElementById('editar-grupo-disponiveis');
    const cores      = ['bg-indigo-700', 'bg-emerald-700', 'bg-pink-700', 'bg-amber-700', 'bg-purple-700'];
    const ids        = membros.map(function(u) { return u.id; });

    divMembros.innerHTML = membros.length ? membros.map(function(u) {
        const ehEu    = u.id === CURRENT_USER_ID;
        const removeBtn = !ehEu
            ? '<button onclick="removerMembroGrupo(' + u.id + ')" class="text-gray-600 hover:text-red-400 transition p-1 rounded-lg hover:bg-red-500/10" title="Remover">'
              + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
              + '</button>'
            : '<span class="text-xs text-gray-600">você</span>';
        return '<div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-gray-800">'
            + '<div class="w-8 h-8 ' + cores[u.id % cores.length] + ' rounded-lg flex items-center justify-center text-xs font-bold shrink-0">' + u.nome.charAt(0).toUpperCase() + '</div>'
            + '<div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">' + u.nome + '</p>'
            + '<p class="text-xs text-gray-400">' + (u.setor || u.papel) + '</p></div>'
            + removeBtn + '</div>';
    }).join('') : '<p class="text-xs text-gray-500 px-2">Nenhum membro.</p>';

    const disponiveis = todos.filter(function(u) { return !ids.includes(u.id); });
    divDisp.innerHTML = disponiveis.length ? disponiveis.map(function(u) {
        return '<button onclick="adicionarMembroGrupo(' + u.id + ')" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-800 transition text-left">'
            + '<div class="w-8 h-8 ' + cores[u.id % cores.length] + ' rounded-lg flex items-center justify-center text-xs font-bold shrink-0">' + u.nome.charAt(0).toUpperCase() + '</div>'
            + '<div class="flex-1 min-w-0"><p class="text-sm font-medium text-white truncate">' + u.nome + '</p>'
            + '<p class="text-xs text-gray-400">' + (u.setor || u.papel) + '</p></div>'
            + '<span class="text-xs text-indigo-400 shrink-0">+ Adicionar</span>'
            + '</button>';
    }).join('') : '<p class="text-xs text-gray-500 px-2">Todos os usuários já estão no grupo.</p>';
}

async function salvarNomeGrupo() {
    const nome = document.getElementById('editar-grupo-nome').value.trim();
    if (!nome) { alert('Informe o nome do grupo.'); return; }
    const res = await fetch('/api/conversas/' + grupoEditandoId, {
        method:  'PATCH',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'nome=' + encodeURIComponent(nome),
    });
    if (res.ok) {
        grupoEditandoNome = nome;
        document.getElementById('editar-grupo-subtitulo').textContent = nome;
        await carregarConversas();
    }
}

async function adicionarMembroGrupo(usuarioId) {
    const res = await fetch('/api/conversas/' + grupoEditandoId + '/participantes', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'usuario_id=' + usuarioId,
    });
    if (res.ok) await carregarMembrosGrupo();
}

async function removerMembroGrupo(usuarioId) {
    if (!confirm('Remover este membro do grupo?')) return;
    const res  = await fetch('/api/conversas/' + grupoEditandoId + '/participantes/' + usuarioId, { method: 'DELETE' });
    const data = await res.json();
    if (!res.ok) { alert(data.erro); return; }
    await carregarMembrosGrupo();
}

async function confirmarExcluirGrupo() {
    if (!confirm('Excluir o grupo "' + grupoEditandoNome + '" permanentemente? Todas as mensagens serão perdidas.')) return;
    const res = await fetch('/api/conversas/' + grupoEditandoId, { method: 'DELETE' });
    if (res.ok) {
        fecharModalEditarGrupo();
        if (conversaAtualId == grupoEditandoId) {
            conversaAtualId = null;
            document.getElementById('messages').innerHTML = '<p class="text-center text-gray-600 text-xs py-8">Selecione uma conversa</p>';
            document.getElementById('chat-nome').textContent = 'Selecione uma conversa';
        }
        carregarConversas();
    }
}
</script>

</body>
</html>