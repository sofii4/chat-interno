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