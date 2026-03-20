<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;

class AdminController
{
    // ── USUÁRIOS ──────────────────────────────

    // GET /api/admin/usuarios
    public function listarUsuarios(Request $request, Response $response): Response
    {
        $pdo  = getDbConnection();
        $stmt = $pdo->query("
            SELECT u.id, u.nome, u.email, u.papel, u.ativo,
                   u.criado_em, s.nome AS setor
            FROM usuarios u
            LEFT JOIN setores s ON s.id = u.setor_id
            ORDER BY u.nome ASC
        ");
        return Json::json($response, $stmt->fetchAll());
    }

    // POST /api/admin/usuarios
    public function criarUsuario(Request $request, Response $response): Response
    {
        $data  = (array) $request->getParsedBody();
        $nome  = trim($data['nome']  ?? '');
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';
        $papel = $data['papel'] ?? 'usuario';
        $setorId = $data['setor_id'] ? (int) $data['setor_id'] : null;

        if (!$nome || !$email || !$senha) {
            return Json::erro($response, 'Nome, e-mail e senha são obrigatórios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Json::erro($response, 'E-mail inválido');
        }

        if (strlen($senha) < 6) {
            return Json::erro($response, 'Senha deve ter ao menos 6 caracteres');
        }

        $papeisValidos = ['admin', 'ti', 'usuario'];
        if (!in_array($papel, $papeisValidos, true)) {
            $papel = 'usuario';
        }

        $pdo = getDbConnection();

        // Verifica e-mail duplicado
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            return Json::erro($response, 'E-mail já cadastrado');
        }

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha_hash, setor_id, papel)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nome,
            $email,
            password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]),
            $setorId,
            $papel,
        ]);

        $id = (int) $pdo->lastInsertId();
        return Json::json($response, ['id' => $id, 'nome' => $nome, 'email' => $email, 'papel' => $papel], 201);
    }

    // PATCH /api/admin/usuarios/{id}
    public function atualizarUsuario(Request $request, Response $response, array $args): Response
    {
        $id   = (int) $args['id'];
        $data = (array) $request->getParsedBody();
        $pdo  = getDbConnection();

        $campos = [];
        $values = [];

        if (!empty($data['nome'])) {
            $campos[] = 'nome = ?';
            $values[] = trim($data['nome']);
        }
        if (!empty($data['papel'])) {
            $campos[] = 'papel = ?';
            $values[] = $data['papel'];
        }
        if (isset($data['setor_id'])) {
            $campos[] = 'setor_id = ?';
            $values[] = $data['setor_id'] ? (int) $data['setor_id'] : null;
        }
        if (isset($data['ativo'])) {
            $campos[] = 'ativo = ?';
            $values[] = (int) $data['ativo'];
        }
        if (!empty($data['senha'])) {
            if (strlen($data['senha']) < 6) {
                return Json::erro($response, 'Senha deve ter ao menos 6 caracteres');
            }
            $campos[] = 'senha_hash = ?';
            $values[] = password_hash($data['senha'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (empty($campos)) {
            return Json::erro($response, 'Nenhum campo para atualizar');
        }

        $values[] = $id;
        $stmt = $pdo->prepare("UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?");
        $stmt->execute($values);

        return Json::json($response, ['ok' => true]);
    }

    // DELETE /api/admin/usuarios/{id} — desativa ao invés de deletar
    public function desativarUsuario(Request $request, Response $response, array $args): Response
    {
        $id      = (int) $args['id'];
        $myId    = $request->getAttribute('user_id');

        if ($id === $myId) {
            return Json::erro($response, 'Você não pode desativar sua própria conta');
        }

        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return Json::json($response, ['ok' => true]);
    }

    // ── SETORES ───────────────────────────────

    // GET /api/admin/setores
    public function listarSetores(Request $request, Response $response): Response
    {
        $pdo  = getDbConnection();
        $stmt = $pdo->query("
            SELECT s.id, s.nome, s.descricao,
                   COUNT(u.id) AS total_usuarios
            FROM setores s
            LEFT JOIN usuarios u ON u.setor_id = s.id AND u.ativo = 1
            GROUP BY s.id
            ORDER BY s.nome ASC
        ");
        return Json::json($response, $stmt->fetchAll());
    }

    // POST /api/admin/setores
    public function criarSetor(Request $request, Response $response): Response
    {
        $data     = (array) $request->getParsedBody();
        $nome     = trim($data['nome'] ?? '');
        $descricao = trim($data['descricao'] ?? '');

        if (!$nome) {
            return Json::erro($response, 'Nome do setor é obrigatório');
        }

        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO setores (nome, descricao) VALUES (?, ?)");
        $stmt->execute([$nome, $descricao]);

        return Json::json($response, [
            'id'   => (int) $pdo->lastInsertId(),
            'nome' => $nome,
        ], 201);
    }

    // DELETE /api/admin/setores/{id}
    public function deletarSetor(Request $request, Response $response, array $args): Response
    {
        $id  = (int) $args['id'];
        $pdo = getDbConnection();

        // Verifica se tem usuários
        $check = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE setor_id = ? AND ativo = 1");
        $check->execute([$id]);
        if ((int) $check->fetch()['total'] > 0) {
            return Json::erro($response, 'Setor possui usuários ativos. Mova-os antes de deletar.');
        }

        $stmt = $pdo->prepare("DELETE FROM setores WHERE id = ?");
        $stmt->execute([$id]);

        return Json::json($response, ['ok' => true]);
    }
}
