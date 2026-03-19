<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;

class ChatController
{
    // GET /api/conversas
    // Retorna todas as conversas que o usuário logado participa
    public function listarConversas(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $pdo    = getDbConnection();

        $stmt = $pdo->prepare("
            SELECT
                c.id,
                c.tipo,
                c.nome,
                -- pega o conteúdo da última mensagem
                (SELECT m.conteudo FROM mensagens m
                 WHERE m.conversa_id = c.id
                 ORDER BY m.criado_em DESC LIMIT 1) AS ultima_mensagem,
                -- conta mensagens não lidas (por ora retorna 0, implementamos depois)
                0 AS nao_lidas
            FROM conversas c
            INNER JOIN participantes p ON p.conversa_id = c.id
            WHERE p.usuario_id = ?
            ORDER BY c.criado_em ASC
        ");
        $stmt->execute([$userId]);
        $conversas = $stmt->fetchAll();

        return Json::json($response, $conversas);
    }

    // GET /api/mensagens?conversa_id=1&pagina=1
    // Retorna o histórico de mensagens de uma conversa (50 por vez)
    public function listarMensagens(Request $request, Response $response): Response
    {
        $userId      = $request->getAttribute('user_id');
        $params      = $request->getQueryParams();
        $conversaId  = (int) ($params['conversa_id'] ?? 0);
        $pagina      = max(1, (int) ($params['pagina'] ?? 1));
        $porPagina   = 50;
        $offset      = ($pagina - 1) * $porPagina;

        if (!$conversaId) {
            return Json::erro($response, 'conversa_id é obrigatório');
        }

        $pdo = getDbConnection();

        // Verifica se o usuário participa dessa conversa (segurança!)
        $check = $pdo->prepare("
            SELECT 1 FROM participantes
            WHERE conversa_id = ? AND usuario_id = ?
        ");
        $check->execute([$conversaId, $userId]);
        if (!$check->fetch()) {
            return Json::erro($response, 'Acesso negado', 403);
        }

        $stmt = $pdo->prepare("
            SELECT
                m.id,
                m.conteudo,
                m.arquivo_path,
                m.arquivo_nome,
                m.criado_em,
                u.id   AS usuario_id,
                u.nome AS usuario_nome
            FROM mensagens m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.conversa_id = ?
            ORDER BY m.criado_em DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$conversaId, $porPagina, $offset]);
        // Inverte para mostrar as mais antigas primeiro
        $mensagens = array_reverse($stmt->fetchAll());

        return Json::json($response, $mensagens);
    }

    // POST /api/mensagens
    // Salva uma nova mensagem no banco
    public function enviarMensagem(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $data   = (array) $request->getParsedBody();

        $conversaId = (int) ($data['conversa_id'] ?? 0);
        $conteudo   = trim($data['conteudo'] ?? '');

        // Validações
        if (!$conversaId || !$conteudo) {
            return Json::erro($response, 'conversa_id e conteudo são obrigatórios');
        }

        if (mb_strlen($conteudo) > 5000) {
            return Json::erro($response, 'Mensagem muito longa (máximo 5000 caracteres)');
        }

        $pdo = getDbConnection();

        // Segurança: verifica se o usuário é participante
        $check = $pdo->prepare("
            SELECT 1 FROM participantes
            WHERE conversa_id = ? AND usuario_id = ?
        ");
        $check->execute([$conversaId, $userId]);
        if (!$check->fetch()) {
            return Json::erro($response, 'Acesso negado', 403);
        }

        // Salva a mensagem
        $stmt = $pdo->prepare("
            INSERT INTO mensagens (conversa_id, usuario_id, conteudo)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$conversaId, $userId, $conteudo]);
        $msgId = (int) $pdo->lastInsertId();

        // Retorna a mensagem completa (com nome do usuário) para o frontend
        $nova = $pdo->prepare("
            SELECT
                m.id, m.conteudo, m.criado_em,
                u.id AS usuario_id, u.nome AS usuario_nome
            FROM mensagens m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.id = ?
        ");
        $nova->execute([$msgId]);

        return Json::json($response, $nova->fetch(), 201);
    }

    // GET /api/usuarios/online
    // Lista usuários para mostrar na sidebar
    public function listarUsuarios(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $pdo    = getDbConnection();

        $stmt = $pdo->prepare("
            SELECT
                u.id,
                u.nome,
                u.papel,
                s.nome AS setor
            FROM usuarios u
            LEFT JOIN setores s ON s.id = u.setor_id
            WHERE u.ativo = 1 AND u.id != ?
            ORDER BY u.nome ASC
        ");
        $stmt->execute([$userId]);

        return Json::json($response, $stmt->fetchAll());
    }
}
