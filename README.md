# Chat Interno + Sistema de Chamados

Sistema interno de comunicação empresarial com chat em tempo real e gestão completa de chamados de TI, incluindo triagem, classificação, comentários técnicos com anexos, histórico, relatórios e notificações.

## Sumário

- [Visão Geral](#visão-geral)
- [Stack Técnica](#stack-técnica)
- [Funcionalidades](#funcionalidades)
- [Modelo de Dados](#modelo-de-dados)
- [APIs REST](#apis-rest)
- [Tempo Real (WebSocket)](#tempo-real-websocket)
- [Como Utilizar](#como-utilizar)
- [Execução Direta na VM](#execução-direta-na-vm)
- [Arquitetura e Operação](#arquitetura-e-operação)

## Visão Geral

O projeto reúne duas frentes principais:

1. Chat corporativo com conversas privadas, grupos e canais por setor.
2. Fluxo de chamados para TI com classificação por prioridade/categoria/subcategoria, anexos, comentários técnicos e finalização com notificação automática ao solicitante.

## Stack Técnica

| Camada | Tecnologia | Função |
|---|---|---|
| Backend | PHP 8.1+ | Aplicação principal |
| Framework | Slim 4 | Rotas e middlewares |
| Tempo real | Ratchet | WebSocket para mensagens/eventos |
| Banco | MySQL 8 | Persistência |
| Acesso a dados | PDO | Consultas com prepared statements |
| Dependências | Composer | Gerenciamento de pacotes |
| Configuração | phpdotenv | Variáveis de ambiente |
| Frontend | HTML + Tailwind CSS + JS Vanilla | Interface e interação |
| Web server | Nginx + PHP-FPM | Entrega HTTP no fluxo containerizado |

## Funcionalidades

### Chat

- Mensagens em tempo real via WebSocket.
- Fallback HTTP para envio quando o socket está indisponível.
- Conversas privadas, em grupo e por setor.
- Indicadores de não lidas e presença online.
- Anexos em mensagens.

### Chamados

- Abertura de chamado com múltiplos anexos.
- Validação de arquivo por tipo MIME e tamanho.
- Classificação por prioridade, categoria e subcategoria.
- Edição de classificação.
- Finalização com registro de responsável e data de resolução.
- Comentários técnicos por TI/Admin com anexos em cada chamado.
- Em chamados finalizados, comentários ficam somente leitura.
- Em chamados documentados, exclusão de comentários é permitida para TI/Admin.
- Dashboard do usuário final para acompanhar os próprios chamados.
- Cancelamento de chamado pelo solicitante (somente chamados ativos).
- Envio automático de mensagem no chat ao finalizar chamado.

### Dashboard TI

- Triagem de chamados abertos.
- Coluna de chamados documentados/classificados.
- Histórico de chamados resolvidos.
- Filtros por categoria/subcategoria e data.
- Ordenação por urgência e por data.
- Modais de detalhes e classificação com suporte a descrições longas e rolagem.
- Gestão de taxonomias (categorias/subcategorias) integrada ao topo do painel.
- Relatório avançado acessível pelo botão de resultados no topo.

### Admin

- Gestão de usuários (criar, atualizar, desativar).
- Gestão de setores.
- Tabela de usuários com paginação e filtros no servidor.
- Busca de usuários por nome, e-mail, setor e papel.

### Relatórios

- Dashboard avançado de chamados (`/dashboard-ti/relatorio`) com KPIs (total, abertos, resolvidos, cancelados, tempo médio).
- Série temporal de abertos vs resolvidos (30 dias).
- Distribuição por categoria.
- Tabelas analíticas por categoria, subcategoria, solicitante e finalizador.
- Exportação de relatório em CSV (API).
- Exportação de relatório em PDF (cliente).

### UX/Tema

- Light mode centralizado em arquivo único para manutenção simples: `public/assets/css/light-mode.css`.
- Todas as telas principais reutilizam o mesmo arquivo, sem duplicação de regras por template.

## Modelo de Dados

Tabelas principais:

- setores
- usuarios
- conversas
- participantes
- mensagens
- chamados
- chamado_anexos
- chamado_comentarios
- chamado_comentario_anexos
- chamado_taxonomias

Pontos relevantes:

- participantes.ultima_leitura controla contagem de não lidas.
- mensagens guarda metadados de arquivo (path/nome/tipo/tamanho), enquanto o binário fica em disco.
- chamado_anexos armazena metadados dos anexos de chamados.
- chamado_comentarios armazena comentários técnicos e de resolução por chamado.
- chamado_comentario_anexos armazena anexos vinculados a comentários.
- Senhas são persistidas com password_hash.

## APIs REST

Todas as rotas exigem sessão autenticada via middleware.

### Chat

| Método | Rota | Descrição |
|---|---|---|
| GET | /api/conversas | Lista conversas do usuário |
| POST | /api/conversas | Cria conversa (grupo/privada) |
| GET | /api/conversas/{id} | Detalhes da conversa |
| PATCH | /api/conversas/{id} | Edita metadados |
| PATCH | /api/conversas/{id}/descricao | Atualiza descrição |
| DELETE | /api/conversas/{id} | Remove conversa |
| POST | /api/conversas/{id}/lida | Marca como lida |
| GET | /api/conversas/{id}/participantes | Lista participantes |
| POST | /api/conversas/{id}/participantes | Adiciona participante |
| DELETE | /api/conversas/{id}/participantes/{uid} | Remove participante |
| GET | /api/mensagens | Lista mensagens por conversa |
| POST | /api/mensagens | Envia mensagem (fallback HTTP) |
| DELETE | /api/mensagens/{id} | Remove mensagem |
| GET | /api/usuarios/online | Lista usuários online |

### Chamados

| Método | Rota | Descrição |
|---|---|---|
| POST | /api/chamados | Abre chamado com anexos |
| GET | /api/chamados | Lista chamados por escopo de perfil |
| GET | /api/chamados/{id}/anexos | Lista todos os anexos do chamado |
| GET | /api/chamados/{id}/comentarios | Lista comentários (TI/Admin e dono do chamado) |
| POST | /api/chamados/{id}/comentarios | Adiciona comentário (TI/Admin) |
| DELETE | /api/chamados/{id}/comentarios/{comentarioId} | Remove comentário (TI/Admin; bloqueado em resolvidos) |
| PATCH | /api/chamados/{id}/cancelar | Cancela chamado do próprio usuário (se ativo) |
| PATCH | /api/chamados/{id}/status | Atualiza status |
| PATCH | /api/chamados/{id}/classificar | Classifica chamado aberto |
| PATCH | /api/chamados/{id}/classificacao | Atualiza classificação |
| PATCH | /api/chamados/{id}/finalizar | Finaliza chamado e notifica no chat |
| GET | /api/chamados/relatorio | Retorna dados agregados do relatório avançado |
| GET | /api/chamados/relatorio/csv | Exporta relatório em CSV |
| GET | /api/chamados-taxonomias | Lista mapa categoria/subcategoria |
| GET | /api/chamados-taxonomias/detalhe | Lista taxonomias com ID |
| POST | /api/chamados-taxonomias | Cria/reativa taxonomia |
| DELETE | /api/chamados-taxonomias/{id} | Inativa taxonomia |

### Admin

| Método | Rota | Descrição |
|---|---|---|
| GET | /api/admin/usuarios | Lista usuários (com paginação/filtros) |
| POST | /api/admin/usuarios | Cria usuário |
| PATCH | /api/admin/usuarios/{id} | Atualiza usuário |
| DELETE | /api/admin/usuarios/{id} | Desativa usuário |
| GET | /api/admin/setores | Lista setores |
| POST | /api/admin/setores | Cria setor |
| DELETE | /api/admin/setores/{id} | Remove setor |

## Tempo Real (WebSocket)

Servidor em processo separado, padrão na porta 8080.

Comando:

```bash
php bin/chat-server.php
```

Eventos principais:

| Direção | Evento | Descrição |
|---|---|---|
| cliente -> servidor | auth | Autentica conexão |
| servidor -> cliente | auth_ok | Confirma autenticação |
| cliente -> servidor | join | Entra em conversa |
| cliente -> servidor | send_message | Envia mensagem |
| servidor -> cliente | new_message | Nova mensagem |
| cliente -> servidor | typing | Usuário digitando |
| servidor -> cliente | typing | Broadcast de digitação |

## Como Utilizar

### Fluxo Containerizado

1. Copie o arquivo de ambiente de exemplo e ajuste os segredos.

```bash
cp .env.docker.example .env
nano .env
```

2. Suba a stack Docker.

```bash
docker compose up -d
docker compose ps
```

3. Acesse a aplicação pela porta publicada no host da VM.

```text
http://localhost:8188/login
```

4. Verifique o WebSocket.

```text
ws://localhost:8080
```

5. Para reiniciar somente os serviços principais.

```bash
docker compose restart php nginx websocket
```

### Rotas de acesso

| URL | Descrição |
|---|---|
| http://localhost:8188/login | Login |
| http://localhost:8188/chat | Chat |
| http://localhost:8188/dashboard-ti | Dashboard TI |
| http://localhost:8188/dashboard-ti/relatorio | Relatório de Chamados |
| http://localhost:8188/meus-chamados | Dashboard do Usuário |
| http://localhost:8188/admin | Admin |


## Arquitetura e Operação

- HTTP e WebSocket rodam em processos separados.
- Histórico e consultas via HTTP; eventos novos via WebSocket.
- Notificações usam combinação de evento em tempo real + sincronização leve periódica no frontend para robustez.
- Arquivos são salvos em disco e apenas metadados ficam no banco.
- Projeto opera com timezone America/Sao_Paulo no app e no serviço de chat.