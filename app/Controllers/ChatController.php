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

        // Em conversas privadas o nome é o do outro usuário
        $stmt = $pdo->prepare("
            SELECT
                c.id,
                c.tipo,
                CASE
                    WHEN c.tipo = 'privada' THEN (
                        SELECT u.nome FROM usuarios u
                        INNER JOIN participantes p2 ON p2.usuario_id = u.id
                        WHERE p2.conversa_id = c.id AND u.id != ?
                        LIMIT 1
                    )
                    ELSE c.nome
                END AS nome,
                (SELECT m.conteudo FROM mensagens m
                 WHERE m.conversa_id = c.id
                 ORDER BY m.criado_em DESC LIMIT 1) AS ultima_mensagem,
                (SELECT COUNT(*) FROM mensagens m
                 WHERE m.conversa_id = c.id
                 AND m.usuario_id != ?
                 AND (p.ultima_leitura IS NULL OR m.criado_em > p.ultima_leitura)
                ) AS nao_lidas
            FROM conversas c
            INNER JOIN participantes p ON p.conversa_id = c.id
            WHERE p.usuario_id = ?
            ORDER BY c.tipo ASC, c.criado_em ASC
        ");
        $stmt->execute([$userId, $userId, $userId]);
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

    // POST /api/conversas
    // Cria grupo (admin) ou conversa privada (qualquer usuário)
    public function criarConversa(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $papel  = $request->getAttribute('user_papel');
        $data   = (array) $request->getParsedBody();

        $tipo = $data['tipo'] ?? 'privada';

        // Só admin cria grupos
        if ($tipo === 'grupo' && $papel !== 'admin') {
            return Json::erro($response, 'Apenas administradores podem criar grupos', 403);
        }

        $pdo = getDbConnection();

        // Conversa privada: verifica se já existe entre os dois usuários
        if ($tipo === 'privada') {
            $outroId = (int) ($data['usuario_id'] ?? 0);
            if (!$outroId || $outroId === $userId) {
                return Json::erro($response, 'Informe um usuário válido');
            }

            // Checa se já existe conversa privada entre eles
            $check = $pdo->prepare("
                SELECT c.id FROM conversas c
                INNER JOIN participantes p1 ON p1.conversa_id = c.id AND p1.usuario_id = ?
                INNER JOIN participantes p2 ON p2.conversa_id = c.id AND p2.usuario_id = ?
                WHERE c.tipo = 'privada'
                LIMIT 1
            ");
            $check->execute([$userId, $outroId]);
            $existente = $check->fetch();

            if ($existente) {
                return Json::json($response, ['id' => $existente['id'], 'ja_existe' => true]);
            }

            // Cria conversa privada
            $stmt = $pdo->prepare("INSERT INTO conversas (tipo, nome, criado_por) VALUES ('privada', NULL, ?)");
            $stmt->execute([$userId]);
            $conversaId = (int) $pdo->lastInsertId();

            // Adiciona os dois participantes
            $pdo->prepare("INSERT INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")
                ->execute([$conversaId, $userId]);
            $pdo->prepare("INSERT INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")
                ->execute([$conversaId, $outroId]);

            // Busca o nome do outro usuário para retornar
            $outro = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
            $outro->execute([$outroId]);
            $nomeOutro = $outro->fetchColumn();

            return Json::json($response, [
                'id'        => $conversaId,
                'tipo'      => 'privada',
                'nome'      => $nomeOutro,
                'ja_existe' => false,
            ], 201);
        }

        // Grupo
        $nome = trim($data['nome'] ?? '');
        if (!$nome) {
            return Json::erro($response, 'Nome do grupo é obrigatório');
        }

        $stmt = $pdo->prepare("INSERT INTO conversas (tipo, nome, criado_por) VALUES ('grupo', ?, ?)");
        $stmt->execute([$nome, $userId]);
        $conversaId = (int) $pdo->lastInsertId();

        // Admin entra no grupo automaticamente
        $pdo->prepare("INSERT INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")
            ->execute([$conversaId, $userId]);

        // Adiciona participantes iniciais se informados
        $participantes = $data['participantes'] ?? '';
        if ($participantes) {
            $ids = array_filter(array_map('intval', explode(',', $participantes)));
            foreach ($ids as $pid) {
                if ($pid === $userId) continue;
                $pdo->prepare("INSERT IGNORE INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")
                    ->execute([$conversaId, $pid]);
            }
        }

        return Json::json($response, [
            'id'   => $conversaId,
            'tipo' => 'grupo',
            'nome' => $nome,
        ], 201);
    }

    // POST /api/conversas/{id}/participantes
    public function adicionarParticipante(Request $request, Response $response, array $args): Response
    {
        $papel     = $request->getAttribute('user_papel');
        $conversaId = (int) $args['id'];
        $data      = (array) $request->getParsedBody();
        $usuarioId = (int) ($data['usuario_id'] ?? 0);

        if ($papel !== 'admin') {
            return Json::erro($response, 'Apenas administradores podem adicionar participantes', 403);
        }

        if (!$usuarioId) {
            return Json::erro($response, 'usuario_id é obrigatório');
        }

        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("INSERT IGNORE INTO participantes (conversa_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$conversaId, $usuarioId]);

        return Json::json($response, ['ok' => true]);
    }


    // DELETE /api/conversas/{id}
    public function deletarConversa(Request $request, Response $response, array $args): Response
    {
        $papel     = $request->getAttribute('user_papel');
        $userId    = $request->getAttribute('user_id');
        $conversaId = (int) $args['id'];

        if ($papel !== 'admin') {
            return Json::erro($response, 'Apenas administradores podem excluir grupos', 403);
        }

        $pdo  = getDbConnection();

        // Verifica se é grupo (não permite deletar privadas)
        $check = $pdo->prepare("SELECT tipo FROM conversas WHERE id = ?");
        $check->execute([$conversaId]);
        $conversa = $check->fetch();

        if (!$conversa) {
            return Json::erro($response, 'Conversa não encontrada', 404);
        }

        if ($conversa['tipo'] === 'privada') {
            return Json::erro($response, 'Não é possível excluir conversas privadas');
        }

        // Deleta tudo em cascata (participantes e mensagens via FK)
        $pdo->prepare("DELETE FROM conversas WHERE id = ?")->execute([$conversaId]);

        return Json::json($response, ['ok' => true]);
    }


    // POST /api/conversas/{id}/lida
    public function marcarComoLida(Request $request, Response $response, array $args): Response
    {
        $userId     = $request->getAttribute('user_id');
        $conversaId = (int) $args['id'];

        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("
            UPDATE participantes SET ultima_leitura = NOW()
            WHERE conversa_id = ? AND usuario_id = ?
        ");
        $stmt->execute([$conversaId, $userId]);

        return Json::json($response, ['ok' => true]);
    }

}