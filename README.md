# Chat Interno + Sistema de Chamados

Sistema interno de comunicação empresarial com chat em tempo real e fluxo completo de chamados para TI, com foco em triagem, classificação, histórico e gestão de taxonomias no dashboard.

## Sumário

- [Stack técnica](#stack-técnica)
- [Funcionalidades principais](#funcionalidades-principais)
- [Novidades do dashboard TI](#novidades-do-dashboard-ti)
- [Banco de dados](#banco-de-dados)
- [APIs REST](#apis-rest)
- [WebSocket](#websocket)
- [Como rodar](#como-rodar)
- [Observações de arquitetura](#observações-de-arquitetura)

## Stack Técnica

| Camada | Tecnologia | Função |
|---|---|---|
| Backend | PHP 8.1+ | Linguagem principal |
| Framework | Slim 4 | Roteamento HTTP |
| Tempo real | Ratchet | Servidor WebSocket (porta 8080) |
| Banco | MySQL 8 | Persistência de dados |
| Acesso a dados | PDO | Queries com prepared statements |
| Dependências | Composer | Gerenciador de pacotes |
| Variáveis | phpdotenv | Configurações via `.env` |
| Frontend | Tailwind CSS (CDN) | UI responsiva |
| Frontend | JavaScript Vanilla | Lógica de interface |
| Servidor web | Apache 2.4 + mod_rewrite | Entrega da aplicação |

## Funcionalidades Principais

- Chat em tempo real com fallback HTTP quando WebSocket estiver offline.
- Conversas privadas, em grupo e por setor.
- Chamados com upload de anexos e classificação por prioridade/categoria/subcategoria.
- Painel admin para gestão de usuários e setores.
- Dashboard TI dedicado para triagem e resolução operacional dos chamados.

## Dashboard TI

### Organização em 3 áreas

- Coluna de triagem para chamados abertos.
- Grade de chamados documentados (classificados).
- Painel lateral de histórico (resolvidos), com opção de minimizar/expandir.

### Filtros e ordenação

- Filtro por categoria (setor).
- Filtro dependente por subcategoria (carrega conforme a categoria selecionada).
- Ordenação dos chamados documentados por urgência quando nenhum filtro de data está selecionado (`critica` -> `alta` -> `media` -> `baixa`).
- Ordenação por data quando selecionado no filtro (`Mais recentes` ou `Mais antigos`).

### Fluxo de classificação e detalhamento

- Modal de classificação com descrição completa, prioridade, categoria e subcategoria.
- Modal de detalhes com ações rápidas para editar classificação, chamar setor e finalizar chamado.
- O botão chamar setor redireciona para chat privado com o solicitante.
- Exibição de data no card e de "resolvido por" no histórico.

### Anexos (dashboard e chat)

- Pré-visualização de imagem no modal (quando o anexo for imagem).
- Botões de visualizar e baixar anexo nos modais.
- Suporte a anexos no cadastro do chamado via chat.
- Compatibilidade com campos de upload `anexos` e `anexos[]`.
- Retorno de erros por arquivo em `anexo_erros` para diagnóstico no frontend.

### Finalização com notificação automática

- Ao finalizar um chamado, o sistema tenta obter/criar conversa privada com o solicitante.
- Uma mensagem automática é enviada no chat do usuário avisando a conclusão do chamado.

### Gestão de taxonomias (categorias/subcategorias)

- Modal "Gerenciar categorias" no dashboard.
- Cadastro e remoção de categoria/subcategoria via API.
- Leitura dinâmica para preencher filtros e selects de classificação.

## Banco de Dados

### Tabelas

```sql
setores             -- Estrutura organizacional
usuarios            -- Usuários e papéis (admin, ti, usuario)
conversas           -- Conversas (privada, grupo, setor)
participantes       -- Usuário x conversa
mensagens           -- Mensagens e anexos
chamados            -- Chamados de suporte
chamado_anexos      -- Anexos dos chamados
chamado_taxonomias  -- Categorias/subcategorias usadas no dashboard
```

### Detalhes importantes

- `participantes.ultima_leitura` controla não lidas por conversa.
- Em conversa privada, `conversas.nome` pode ser `NULL`.
- Senhas são armazenadas com `password_hash`.
- Na listagem de chamados, o backend retorna metadados do primeiro anexo para exibição rápida no dashboard.

## APIs REST

Todas as rotas abaixo exigem sessão autenticada (`AuthMiddleware`).

### Chat

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/conversas` | Lista conversas do usuário |
| `POST` | `/api/conversas` | Cria conversa (grupo/privada) |
| `GET` | `/api/conversas/{id}` | Detalhes de conversa |
| `PATCH` | `/api/conversas/{id}` | Edita metadados da conversa |
| `PATCH` | `/api/conversas/{id}/descricao` | Atualiza descrição |
| `DELETE` | `/api/conversas/{id}` | Remove conversa |
| `POST` | `/api/conversas/{id}/lida` | Marca como lida |
| `GET` | `/api/conversas/{id}/participantes` | Lista participantes |
| `POST` | `/api/conversas/{id}/participantes` | Adiciona participante |
| `DELETE` | `/api/conversas/{id}/participantes/{uid}` | Remove participante |
| `GET` | `/api/mensagens` | Lista mensagens por conversa |
| `POST` | `/api/mensagens` | Envia mensagem (fallback HTTP) |
| `DELETE` | `/api/mensagens/{id}` | Apaga mensagem |
| `GET` | `/api/usuarios/online` | Lista usuários online |

### Chamados

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/chamados` | Abre chamado com anexos |
| `GET` | `/api/chamados` | Lista chamados (escopo por papel) |
| `PATCH` | `/api/chamados/{id}/status` | Atualiza status |
| `PATCH` | `/api/chamados/{id}/classificar` | Classifica chamado aberto |
| `PATCH` | `/api/chamados/{id}/classificacao` | Atualiza classificação existente |
| `PATCH` | `/api/chamados/{id}/finalizar` | Finaliza e dispara mensagem automática |
| `GET` | `/api/chamados-taxonomias` | Lista mapa de categorias/subcategorias |
| `GET` | `/api/chamados-taxonomias/detalhe` | Lista taxonomias com ID |
| `POST` | `/api/chamados-taxonomias` | Cria/reativa taxonomia |
| `DELETE` | `/api/chamados-taxonomias/{id}` | Inativa taxonomia |

### Admin

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/admin/usuarios` | Lista usuários |
| `POST` | `/api/admin/usuarios` | Cria usuário |
| `PATCH` | `/api/admin/usuarios/{id}` | Atualiza usuário |
| `DELETE` | `/api/admin/usuarios/{id}` | Desativa usuário |
| `GET` | `/api/admin/setores` | Lista setores |
| `POST` | `/api/admin/setores` | Cria setor |
| `DELETE` | `/api/admin/setores/{id}` | Remove setor |

## WebSocket

Servidor de tempo real executado em processo separado (porta `8080`).

```bash
php bin/chat-server.php
```

### Eventos principais

| Direção | Evento | Descrição |
|---|---|---|
| cliente -> servidor | `auth` | Autentica conexão |
| servidor -> cliente | `auth_ok` | Confirma autenticação |
| cliente -> servidor | `join` | Entra na conversa |
| cliente -> servidor | `send_message` | Envia mensagem |
| servidor -> cliente | `new_message` | Entrega de nova mensagem |
| cliente -> servidor | `typing` | Usuário digitando |
| servidor -> cliente | `typing` | Broadcast de digitação |

Reconexão automática no frontend e fallback para `POST /api/mensagens` quando necessário.

## Como Rodar

### Pré-requisitos

```bash
sudo apt install php8.3 php8.3-mysql php8.3-zip php8.3-sockets apache2 libapache2-mod-php8.3 mysql-server unzip -y
```

### Instalação

```bash
git clone https://github.com/sofii4/chat-interno.git
cd chat-interno

composer install

cp .env.example .env
# editar .env

mysql -u root -p -e "CREATE DATABASE chat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p chat_db < config/schema.sql

sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Permissões para upload (importante)

Se o Apache roda com usuário `www-data`, garanta permissão de escrita em `public/uploads`:

```bash
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 775 public/uploads
```

### Subir WebSocket

```bash
php bin/chat-server.php
```

### Acesso

| URL | Descrição |
|---|---|
| `http://localhost/login` | Login |
| `http://localhost/chat` | Chat |
| `http://localhost/dashboard-ti` | Dashboard TI |
| `http://localhost/admin` | Painel admin |

## Observações de Arquitetura

- Apache (HTTP + APIs) e Ratchet (WebSocket) rodam em paralelo.
- Histórico de mensagens via HTTP; novas mensagens via WebSocket.
- Finalização de chamado mantém rastreabilidade: altera status, registra resolvedor e notifica o usuário no chat.
- Upload valida MIME real e tamanho máximo, e retorna erros detalhados por arquivo.