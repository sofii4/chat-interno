## Refatorações e Melhorias Recentes

Esta seção resume alterações importantes aplicadas para legibilidade, manutenção e correção de inconsistências.

### Organização de código

- Extração de scripts inline dos templates para arquivos dedicados:
	- public/assets/js/chat.js
	- public/assets/js/dashboard-ti.js
	- public/assets/js/admin.js
- Centralização da renderização de templates.
- Centralização de verificação de schema para reduzir duplicação entre camadas.
- Padronização de respostas JSON e serialização segura.

### Correções funcionais

- Suporte robusto a múltiplos anexos em chamados, incluindo compatibilidade de payload em formatos diferentes.
- Endpoint dedicado para listar anexos de um chamado:
	- GET /api/chamados/{id}/anexos
- Exibição de todos os anexos no detalhe do chamado (incluindo imagem e download).
- Ajustes de timezone para America/Sao_Paulo no app HTTP e no servidor WebSocket.
- Ajuste de sessão MySQL para fuso de Brasília.
- Melhorias de tempo real no chat:
	- Sincronização periódica leve para mitigar perda de evento.
	- Evita replay de notificações antigas no carregamento inicial.
	- Detecção de novas conversas com atualização da lista.
- Ajuste de usabilidade da área de escrita para focar ao clicar na caixa, não apenas no texto.

### Limpeza estrutural (abril/2026)

- Remoção de classes placeholder sem uso no runtime:
	- app/Models/Chamado.php
	- app/Models/Mensagem.php
	- app/Models/Usuario.php
	- app/Services/FileUploadService.php
	- app/Middleware/CsrfMiddleware.php
- Remoção de métodos privados mortos no servidor WebSocket que não eram referenciados:
	- obterUltimaMensagemVisivelId
	- obterUltimaConversaVisivelId
- Padronização de legibilidade e consistência em controllers:
	- Ajuste de indentação e organização de bloco SQL em ChatController::listarUsuarios.
	- Normalização robusta de lista de uploads em ChatController::normalizarArquivosUpload para lidar com arrays aninhados.
	- Tipagem explícita de user_id em ChatController::criarConversa.
	- Padronização de blocos de validação em AdminController::listarUsuarios.
	- Ajuste defensivo em AdminController::criarUsuario para leitura de setor_id opcional.

### Garantias de compatibilidade

- Não houve alteração de rotas, contratos de payload, permissões ou regras de negócio dos fluxos já existentes.
- Mudanças focadas em remoção de código não utilizado e melhoria de manutenção/legibilidade.

### Limpeza adicional

- Remoção do template órfão [templates/layout.html](templates/layout.html), que não possuía referência em nenhum ponto do projeto.

### Consolidação de frontend

- Criação de utilitários compartilhados em [public/assets/js/utils.js](public/assets/js/utils.js) para evitar duplicação de `escapeHtml`, `formatarDataHora`, `normalizarTexto` e `formatarDuracaoMinutos`.
- Criação de configuração global em [public/assets/js/config.js](public/assets/js/config.js) para centralizar prioridades, status e categorias base.
- Atualização das telas [templates/chat.php](templates/chat.php), [templates/meus_chamados.php](templates/meus_chamados.php), [templates/dashboard_ti.php](templates/dashboard_ti.php) e [templates/relatorio_chamados.php](templates/relatorio_chamados.php) para carregar os arquivos compartilhados antes dos scripts específicos.
- Remoção de definições duplicadas de helpers e configurações em [public/assets/js/chat.js](public/assets/js/chat.js), [public/assets/js/meus-chamados.js](public/assets/js/meus-chamados.js) e [public/assets/js/dashboard-ti.js](public/assets/js/dashboard-ti.js).

### Consolidação visual

- Centralização de estilos repetidos de scrollbar e cards em [public/assets/css/light-mode.css](public/assets/css/light-mode.css).
- Remoção de blocos `<style>` inline repetidos das páginas [templates/dashboard_ti.php](templates/dashboard_ti.php), [templates/meus_chamados.php](templates/meus_chamados.php) e [templates/relatorio_chamados.php](templates/relatorio_chamados.php).
- Manutenção de regras específicas do chat em [templates/chat.php](templates/chat.php), com scrollbar própria preservada via estilo compartilhado.
- Extração dos gradientes hero repetidos para classes reutilizáveis em [public/assets/css/light-mode.css](public/assets/css/light-mode.css).
- Remoção de botão oculto morto em [public/assets/js/admin.js](public/assets/js/admin.js) que não participava do fluxo.