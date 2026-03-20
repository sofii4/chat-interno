# Chat Interno + Sistema de Chamados

Sistema interno de comunicação empresarial com chat em tempo real e chamados de emergência para TI.

---

## Sumário

- [Stack Técnica](#stack-técnica)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Banco de Dados](#banco-de-dados)
- [APIs REST](#apis-rest)
- [WebSocket](#websocket)
- [Papéis de Usuário](#papéis-de-usuário)
- [Segurança](#segurança)
- [Como Rodar](#como-rodar)

---

## Stack Técnica

| Camada | Tecnologia | Função |
|---|---|---|
| Backend | PHP 8.3 | Linguagem principal |
| Framework | Slim 4 | Roteamento HTTP (micro-framework) |
| Tempo real | Ratchet | Servidor WebSocket (porta 8080) |
| Banco | MySQL 8 | Persistência de dados |
| ORM/Query | PDO | Conexão segura com prepared statements |
| Dependências | Composer | Gerenciador de pacotes (PSR-4) |
| Variáveis | phpdotenv | Configurações via `.env` |
| Frontend | Tailwind CSS (CDN) | Estilização responsiva |
| Frontend | JavaScript Vanilla | Sem frameworks JS |
| Servidor web | Apache 2.4 + mod_rewrite | Serve HTTP e redireciona para `index.php` |
| Versionamento | Git + GitHub | Controle de versão por branches |

---

## Estrutura do Projeto

```
/projeto-chat/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php       # Login, logout, sessão
│   │   ├── ChatController.php       # Conversas, mensagens, participantes
│   │   ├── ChamadoController.php    # Chamados de emergência e anexos
│   │   └── AdminController.php      # Gestão de usuários e setores
│   ├── Middleware/
│   │   ├── AuthMiddleware.php       # Verifica sessão PHP ativa
│   │   └── AdminMiddleware.php      # Verifica papel = 'admin'
│   ├── Services/
│   │   ├── ChatServer.php           # Lógica do servidor Ratchet (WebSocket)
│   │   └── FileUploadService.php    # Upload seguro de arquivos
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Mensagem.php
│   │   └── Chamado.php
│   └── Helpers/
│       └── Response.php             # Respostas JSON padronizadas
├── bin/
│   └── chat-server.php              # Inicia o servidor WebSocket
├── config/
│   ├── database.php                 # Conexão PDO (singleton)
│   └── schema.sql                   # Script de criação do banco
├── public/                          # Único diretório exposto pelo Apache
│   ├── index.php                    # Front controller (todas as rotas passam aqui)
│   ├── .htaccess                    # Rewrite rules do Slim
│   └── uploads/                     # Arquivos enviados por usuários
│       └── .htaccess                # Bloqueia execução de PHP nos uploads
├── templates/
│   ├── login.php                    # Página de login
│   ├── chat.php                     # Interface principal do chat
│   └── admin.php                    # Painel administrativo
├── .env                             # Credenciais (nunca commitar)
├── .gitignore
└── composer.json
```

---

## Banco de Dados

### Tabelas

```sql
setores          -- Grupos organizacionais da empresa
usuarios         -- Usuários com papel (admin, ti, usuario)
conversas        -- Salas de chat (privada, grupo, setor)
participantes    -- Relacionamento usuário <-> conversa (com ultima_leitura)
mensagens        -- Histórico de mensagens com suporte a anexos
chamados         -- Chamados de emergência para TI
chamado_anexos   -- Arquivos anexados aos chamados
```

### Detalhes importantes

- `participantes.ultima_leitura` — controla o contador de mensagens não lidas por conversa por usuário
- `conversas.tipo` — pode ser `privada`, `grupo` ou `setor`
- Em conversas privadas o campo `nome` é `NULL` — o nome exibido é buscado dinamicamente do outro participante
- Senhas armazenadas com `password_hash()` bcrypt (cost 12), nunca em texto puro

---

## APIs REST

Todas as rotas abaixo exigem sessão autenticada (`AuthMiddleware`). Rotas `/api/admin/*` exigem papel `admin` (`AdminMiddleware`).

### Chat

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/conversas` | Lista conversas do usuário logado com contagem de não lidas |
| `POST` | `/api/conversas` | Cria grupo (admin) ou conversa privada (todos) |
| `DELETE` | `/api/conversas/{id}` | Deleta grupo — somente admin |
| `POST` | `/api/conversas/{id}/lida` | Marca conversa como lida (zera badge) |
| `POST` | `/api/conversas/{id}/participantes` | Adiciona participante a um grupo — somente admin |
| `GET` | `/api/mensagens?conversa_id=X` | Histórico paginado (50 por página) |
| `POST` | `/api/mensagens` | Envia mensagem via HTTP (fallback quando WebSocket offline) |
| `GET` | `/api/usuarios/online` | Lista usuários para sidebar e modais |

### Chamados

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/chamados` | Abre chamado com título, descrição e anexos |
| `GET` | `/api/chamados` | TI/admin veem todos; usuário comum vê apenas os seus |
| `PATCH` | `/api/chamados/{id}/status` | Atualiza status — somente TI e admin |

### Admin

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/admin/usuarios` | Lista todos os usuários |
| `POST` | `/api/admin/usuarios` | Cria usuário com senha hasheada |
| `PATCH` | `/api/admin/usuarios/{id}` | Edita nome, papel, setor, senha ou status |
| `DELETE` | `/api/admin/usuarios/{id}` | Desativa usuário (não deleta do banco) |
| `GET` | `/api/admin/setores` | Lista setores com contagem de usuários |
| `POST` | `/api/admin/setores` | Cria setor |
| `DELETE` | `/api/admin/setores/{id}` | Deleta setor (bloqueia se tiver usuários ativos) |

---

## WebSocket

O servidor WebSocket roda como um processo PHP separado na porta `8080` via Ratchet.

**Iniciar:**
```bash
php bin/chat-server.php
```

### Eventos

| Direção | Evento | Descrição |
|---|---|---|
| cliente → servidor | `auth` | Autentica a conexão com `user_id` e `user_nome` |
| servidor → cliente | `auth_ok` | Confirma autenticação |
| cliente → servidor | `join` | Entra em uma conversa (muda de sala) |
| cliente → servidor | `send_message` | Envia mensagem — servidor salva no banco e faz broadcast |
| servidor → cliente | `new_message` | Entregue a todos os participantes conectados da conversa |
| cliente → servidor | `typing` | Usuário está digitando |
| servidor → cliente | `typing` | Exibe "X está digitando..." por 2 segundos |

**Reconexão automática:** o cliente tenta reconectar a cada 3 segundos se a conexão cair.

**Fallback HTTP:** se o WebSocket estiver offline, o envio de mensagens cai automaticamente para `POST /api/mensagens` via Fetch API.

---

## Papéis de Usuário

O sistema possui três papéis definidos na coluna `usuarios.papel`.

### `admin`

- Acesso total ao sistema
- Acessa o painel administrativo em `/admin`
- Cria, edita, desativa e reativa usuários
- Cria e deleta setores
- Cria e deleta grupos de conversa
- Adiciona participantes a grupos
- Abre chamados de emergência
- Vê todos os chamados no sistema
- Ícone de engrenagem ⚙️ visível na sidebar do chat

### `ti`

- Acessa o chat (grupos que participa + conversas privadas)
- Abre chamados de emergência
- Vê e atualiza o status de todos os chamados abertos
- Não acessa o painel admin
- Não cria ou deleta grupos

### `usuario`

- Acessa o chat (grupos que participa + conversas privadas)
- Inicia conversas privadas com qualquer usuário ativo
- Abre chamados de emergência
- Vê apenas seus próprios chamados
- Não acessa o painel admin
- Não cria grupos

---

## Segurança

| Medida | Implementação |
|---|---|
| Senhas | `password_hash()` bcrypt com cost 12 |
| SQL Injection | PDO com prepared statements em todas as queries |
| Session Fixation | `session_regenerate_id(true)` no login |
| Cookie seguro | `session.cookie_httponly = 1` |
| Uploads | Validação de MIME type pelo conteúdo real do arquivo (não pela extensão) |
| Execução de scripts | `.htaccess` em `/public/uploads/` bloqueia execução de PHP |
| Credenciais | Isoladas no `.env` (nunca commitado no Git) |
| Autorização WS | Servidor verifica se usuário é participante antes de aceitar mensagem |
| XSS | Escape de HTML via `htmlspecialchars()` no backend e `textContent` no JS |
| Autorização de rotas | `AuthMiddleware` em todas as rotas protegidas; `AdminMiddleware` nas rotas admin |

---

## Como Rodar

### Pré-requisitos

```bash
sudo apt install php8.3 php8.3-mysql php8.3-zip php8.3-sockets apache2 libapache2-mod-php8.3 mysql-server unzip -y
```

### Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/sofii4/chat-interno.git
cd chat-interno

# 2. Instalar dependências PHP
composer install

# 3. Configurar variáveis de ambiente
cp .env.example .env
# Editar .env com as credenciais do banco

# 4. Criar banco e executar schema
mysql -u root -p -e "CREATE DATABASE chat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p chat_db < config/schema.sql

# 5. Configurar Apache (apontar DocumentRoot para /public)
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Iniciar o servidor WebSocket

```bash
# Em um terminal separado (ou via Supervisor em produção)
php bin/chat-server.php
```

### Acesso

| URL | Descrição |
|---|---|
| `http://localhost/login` | Página de login |
| `http://localhost/chat` | Interface do chat |
| `http://localhost/admin` | Painel admin (requer papel admin) |

**Usuário padrão criado pelo schema:**
- E-mail: `admin@empresa.com`
- Senha: `password` ← **trocar imediatamente em produção**

---

## Observações de Arquitetura

**Dois servidores em paralelo.** O Apache (porta 80) serve o HTML e as APIs REST. O Ratchet (porta 8080) mantém as conexões WebSocket. O browser conecta nos dois simultaneamente.

**Histórico via HTTP, tempo real via WebSocket.** Ao abrir uma conversa, o JS faz `GET /api/mensagens` para buscar o histórico. Apenas mensagens novas trafegam pelo WebSocket — isso evita sobrecarga na conexão persistente.

**Notificações offline via banco.** A coluna `participantes.ultima_leitura` registra quando o usuário leu cada conversa pela última vez. Ao carregar as conversas, a API conta mensagens com `criado_em > ultima_leitura` e retorna o badge com o número correto mesmo para usuários que estavam deslogados.

**Chamados preparados para integração com IA.** A prioridade dos chamados não é definida pelo usuário — o campo existe no banco para ser preenchido futuramente por um agente de IA que classifica automaticamente a urgência com base no título e descrição.