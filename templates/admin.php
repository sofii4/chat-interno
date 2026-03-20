<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Chat Interno</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen">

<!-- Header -->
<header class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="/chat" class="text-gray-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-bold">Painel Administrativo</h1>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm text-gray-400">Olá, <span class="text-white font-medium"><?= htmlspecialchars($userName) ?></span></span>
        <a href="/logout" class="text-xs text-gray-500 hover:text-red-400 transition">Sair</a>
    </div>
</header>

<!-- Tabs -->
<div class="border-b border-gray-800 px-6">
    <div class="flex gap-0">
        <button onclick="trocarAba('usuarios')" id="tab-usuarios"
                class="tab-btn px-5 py-3 text-sm font-medium border-b-2 border-indigo-500 text-indigo-400 transition">
            Usuários
        </button>
        <button onclick="trocarAba('setores')" id="tab-setores"
                class="tab-btn px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-400 hover:text-white transition">
            Setores
        </button>
    </div>
</div>

<main class="max-w-6xl mx-auto px-6 py-8">

    <!-- ABA USUÁRIOS -->
    <div id="aba-usuarios">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Usuários</h2>
            <button onclick="abrirModalUsuario()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl px-4 py-2 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Usuário
            </button>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                        <th class="text-left px-6 py-3">Nome</th>
                        <th class="text-left px-6 py-3">E-mail</th>
                        <th class="text-left px-6 py-3">Setor</th>
                        <th class="text-left px-6 py-3">Papel</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-usuarios" class="divide-y divide-gray-800">
                    <tr><td colspan="6" class="text-center py-8 text-gray-500 text-sm">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ABA SETORES -->
    <div id="aba-setores" class="hidden">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Setores</h2>
            <button onclick="abrirModalSetor()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl px-4 py-2 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Setor
            </button>
        </div>

        <div id="grid-setores" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="text-center py-8 text-gray-500 text-sm">Carregando...</div>
        </div>
    </div>

</main>

<!-- MODAL USUÁRIO -->
<div id="modal-usuario" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <h3 id="modal-usuario-titulo" class="font-bold text-white">Novo Usuário</h3>
            <button onclick="fecharModalUsuario()" class="text-gray-500 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="usuario-id">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nome completo</label>
                <input type="text" id="usuario-nome" placeholder="Ex: Maria Silva"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">E-mail</label>
                <input type="email" id="usuario-email" placeholder="maria@empresa.com"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Senha <span id="senha-hint" class="text-gray-500 font-normal">(mínimo 6 caracteres)</span>
                </label>
                <input type="password" id="usuario-senha" placeholder="••••••••"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Papel</label>
                    <select id="usuario-papel"
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="usuario">Usuário</option>
                        <option value="ti">TI</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Setor</label>
                    <select id="usuario-setor"
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sem setor</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="fecharModalUsuario()"
                    class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl py-2.5 text-sm transition">
                Cancelar
            </button>
            <button id="btn-salvar-usuario" onclick="salvarUsuario()"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl py-2.5 text-sm font-bold transition">
                Salvar
            </button>
        </div>
    </div>
</div>

<!-- MODAL SETOR -->
<div id="modal-setor" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-sm shadow-2xl">
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <h3 class="font-bold text-white">Novo Setor</h3>
            <button onclick="fecharModalSetor()" class="text-gray-500 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nome do setor</label>
                <input type="text" id="setor-nome" placeholder="Ex: Financeiro"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Descrição</label>
                <input type="text" id="setor-descricao" placeholder="Opcional"
                       class="w-full bg-gray-800 border border-gray-700 text-white placeholder-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="fecharModalSetor()"
                    class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl py-2.5 text-sm transition">
                Cancelar
            </button>
            <button onclick="salvarSetor()"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl py-2.5 text-sm font-bold transition">
                Criar Setor
            </button>
        </div>
    </div>
</div>

<script>
const PAPEIS = { admin: 'Admin', ti: 'TI', usuario: 'Usuário' };
const CORES_PAPEL = { admin: 'bg-purple-500/20 text-purple-400', ti: 'bg-blue-500/20 text-blue-400', usuario: 'bg-gray-500/20 text-gray-400' };
let usuarioEditandoId = null;

// ── Tabs ──────────────────────────────────────
function trocarAba(aba) {
    document.getElementById('aba-usuarios').classList.toggle('hidden', aba !== 'usuarios');
    document.getElementById('aba-setores').classList.toggle('hidden',  aba !== 'setores');
    document.getElementById('tab-usuarios').className = `tab-btn px-5 py-3 text-sm font-medium border-b-2 transition ${aba === 'usuarios' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-white'}`;
    document.getElementById('tab-setores').className  = `tab-btn px-5 py-3 text-sm font-medium border-b-2 transition ${aba === 'setores'  ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-white'}`;
    if (aba === 'setores') carregarSetores();
}

// ── Usuários ──────────────────────────────────
async function carregarUsuarios() {
    const res   = await fetch('/api/admin/usuarios');
    const lista = await res.json();
    const tbody = document.getElementById('tabela-usuarios');

    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500 text-sm">Nenhum usuário cadastrado</td></tr>';
        return;
    }

    tbody.innerHTML = lista.map(u => `
        <tr class="hover:bg-gray-800/50 transition">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-700 rounded-lg flex items-center justify-center text-xs font-bold shrink-0">
                        ${u.nome.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-sm font-medium text-white">${u.nome}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-400">${u.email}</td>
            <td class="px-6 py-4 text-sm text-gray-400">${u.setor ?? '—'}</td>
            <td class="px-6 py-4">
                <span class="text-xs font-medium px-2.5 py-1 rounded-lg ${CORES_PAPEL[u.papel] ?? ''}">
                    ${PAPEIS[u.papel] ?? u.papel}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="text-xs font-medium px-2.5 py-1 rounded-lg ${u.ativo ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}">
                    ${u.ativo ? 'Ativo' : 'Inativo'}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <button onclick="editarUsuario(${u.id})"
                            class="text-xs text-indigo-400 hover:text-indigo-300 transition px-2 py-1 rounded-lg hover:bg-indigo-500/10">
                        Editar
                    </button>
                    ${u.ativo ? `
                    <button onclick="desativarUsuario(${u.id}, '${u.nome}')"
                            class="text-xs text-red-400 hover:text-red-300 transition px-2 py-1 rounded-lg hover:bg-red-500/10">
                        Desativar
                    </button>` : ''}
                </div>
            </td>
        </tr>`).join('');
}

function abrirModalUsuario() {
    usuarioEditandoId = null;
    document.getElementById('modal-usuario-titulo').textContent = 'Novo Usuário';
    document.getElementById('senha-hint').textContent = '(mínimo 6 caracteres)';
    document.getElementById('usuario-id').value    = '';
    document.getElementById('usuario-nome').value  = '';
    document.getElementById('usuario-email').value = '';
    document.getElementById('usuario-senha').value = '';
    document.getElementById('usuario-papel').value = 'usuario';
    document.getElementById('usuario-setor').value = '';
    document.getElementById('usuario-email').disabled = false;
    document.getElementById('modal-usuario').classList.remove('hidden');
}

async function editarUsuario(id) {
    const res  = await fetch('/api/admin/usuarios');
    const lista = await res.json();
    const u    = lista.find(x => x.id === id);
    if (!u) return;

    usuarioEditandoId = id;
    document.getElementById('modal-usuario-titulo').textContent = 'Editar Usuário';
    document.getElementById('senha-hint').textContent = '(deixe em branco para não alterar)';
    document.getElementById('usuario-nome').value  = u.nome;
    document.getElementById('usuario-email').value = u.email;
    document.getElementById('usuario-email').disabled = true;
    document.getElementById('usuario-senha').value = '';
    document.getElementById('usuario-papel').value = u.papel;
    document.getElementById('modal-usuario').classList.remove('hidden');

    // Seleciona o setor correto após carregar
    await carregarSetoresNoSelect();
}

function fecharModalUsuario() {
    document.getElementById('modal-usuario').classList.add('hidden');
}

async function salvarUsuario() {
    const btn   = document.getElementById('btn-salvar-usuario');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const body = new URLSearchParams({
        nome:     document.getElementById('usuario-nome').value.trim(),
        email:    document.getElementById('usuario-email').value.trim(),
        senha:    document.getElementById('usuario-senha').value,
        papel:    document.getElementById('usuario-papel').value,
        setor_id: document.getElementById('usuario-setor').value,
    });

    try {
        const url    = usuarioEditandoId ? `/api/admin/usuarios/${usuarioEditandoId}` : '/api/admin/usuarios';
        const method = usuarioEditandoId ? 'PATCH' : 'POST';
        const res    = await fetch(url, { method, headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body });
        const data   = await res.json();

        if (!res.ok) throw new Error(data.erro ?? 'Erro ao salvar');

        fecharModalUsuario();
        carregarUsuarios();
        mostrarToast(usuarioEditandoId ? 'Usuário atualizado!' : 'Usuário criado com sucesso!');
    } catch (err) {
        alert('Erro: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Salvar';
    }
}

async function desativarUsuario(id, nome) {
    if (!confirm(`Desativar o usuário "${nome}"? Ele não conseguirá mais fazer login.`)) return;
    const res = await fetch(`/api/admin/usuarios/${id}`, { method: 'DELETE' });
    if (res.ok) { carregarUsuarios(); mostrarToast('Usuário desativado.'); }
}

// ── Setores ───────────────────────────────────
async function carregarSetores() {
    const res   = await fetch('/api/admin/setores');
    const lista = await res.json();
    const grid  = document.getElementById('grid-setores');

    if (!lista.length) {
        grid.innerHTML = '<div class="text-center py-8 text-gray-500 text-sm col-span-3">Nenhum setor cadastrado</div>';
        return;
    }

    grid.innerHTML = lista.map(s => `
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5 flex items-start justify-between gap-3">
            <div>
                <p class="font-semibold text-white">${s.nome}</p>
                <p class="text-xs text-gray-400 mt-1">${s.descricao ?? 'Sem descrição'}</p>
                <p class="text-xs text-indigo-400 mt-2">${s.total_usuarios} usuário(s)</p>
            </div>
            <button onclick="deletarSetor(${s.id}, '${s.nome}')"
                    class="text-gray-600 hover:text-red-400 transition p-1 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>`).join('');

    await carregarSetoresNoSelect();
}

async function carregarSetoresNoSelect() {
    const res    = await fetch('/api/admin/setores');
    const lista  = await res.json();
    const select = document.getElementById('usuario-setor');
    const atual  = select.value;
    select.innerHTML = '<option value="">Sem setor</option>' +
        lista.map(s => `<option value="${s.id}">${s.nome}</option>`).join('');
    if (atual) select.value = atual;
}

function abrirModalSetor() {
    document.getElementById('setor-nome').value = '';
    document.getElementById('setor-descricao').value = '';
    document.getElementById('modal-setor').classList.remove('hidden');
}

function fecharModalSetor() {
    document.getElementById('modal-setor').classList.add('hidden');
}

async function salvarSetor() {
    const nome     = document.getElementById('setor-nome').value.trim();
    const descricao = document.getElementById('setor-descricao').value.trim();
    if (!nome) { alert('Informe o nome do setor.'); return; }

    const res  = await fetch('/api/admin/setores', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ nome, descricao }),
    });
    const data = await res.json();
    if (!res.ok) { alert(data.erro); return; }

    fecharModalSetor();
    carregarSetores();
    mostrarToast('Setor criado!');
}

async function deletarSetor(id, nome) {
    if (!confirm(`Deletar o setor "${nome}"?`)) return;
    const res  = await fetch(`/api/admin/setores/${id}`, { method: 'DELETE' });
    const data = await res.json();
    if (!res.ok) { alert(data.erro); return; }
    carregarSetores();
    mostrarToast('Setor removido.');
}

// ── Toast ─────────────────────────────────────
function mostrarToast(msg) {
    const t = document.createElement('div');
    t.className = 'fixed bottom-6 right-6 bg-gray-800 border border-green-500/30 text-white rounded-xl px-4 py-3 shadow-2xl z-50 text-sm flex items-center gap-2';
    t.innerHTML = `<span class="text-green-400">✓</span> ${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

// ── Init ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    await carregarSetoresNoSelect();
    carregarUsuarios();
});
</script>
</body>
</html>
