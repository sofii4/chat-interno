<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;
use App\Repositories\ConversaRepository;
use App\Repositories\MensagemRepository;
use App\Repositories\UsuarioRepository;
use App\Validators\MensagemValidator;
use App\DTOs\CreateMessageDTO;
use App\Exceptions\ValidationException;
use App\Exceptions\UnauthorizedException;

class ChatController
{
    public function __construct(
        private ConversaRepository $conversaRepo,
        private MensagemRepository $mensagemRepo,
        private UsuarioRepository $usuarioRepo,
        private MensagemValidator $mensagemValidator,
    ) {}

    /** GET /api/conversas */
    public function listarConversas(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $conversas = $this->conversaRepo->listarPorUsuario($userId);
            return Json::json($response, $conversas);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar conversas', 500);
        }
    }

    /** GET /api/mensagens?conversa_id=1&pagina=1 */
    public function listarMensagens(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $params = $request->getQueryParams();
            $conversaId = (int) ($params['conversa_id'] ?? 0);
            $pagina = max(1, (int) ($params['pagina'] ?? 1));

            if (!$conversaId) {
                return Json::erro($response, 'conversa_id é obrigatório');
            }

            $mensagens = $this->mensagemRepo->listarPorConversa($conversaId, $userId, $pagina);
            return Json::json($response, $mensagens);
        } catch (UnauthorizedException $e) {
            return Json::erro($response, $e->getMessage(), 403);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar mensagens', 500);
        }
    }

    /** POST /api/mensagens */
    public function enviarMensagem(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $data = (array) $request->getParsedBody();

            $dto = CreateMessageDTO::fromArray($data);
            $this->mensagemValidator->validateCreate($dto);

            $msgId = $this->mensagemRepo->criar($dto->conversaId, $userId, $dto);
            $mensagem = $this->mensagemRepo->buscarPorId($msgId);

            return Json::json($response, $mensagem, 201);
        } catch (UnauthorizedException $e) {
            return Json::erro($response, $e->getMessage(), 403);
        } catch (ValidationException $e) {
            return Json::erro($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao enviar mensagem', 500);
        }
    }

    /** GET /api/usuarios/online */
    public function listarUsuarios(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $usuarios = $this->usuarioRepo->listarAtivos($userId);
            return Json::json($response, $usuarios);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar usuários', 500);
        }
    }

    /** POST /api/conversas */
    public function criarConversa(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $papel = $request->getAttribute('user_papel');
            $data = (array) $request->getParsedBody();
            $tipo = $data['tipo'] ?? 'privada';

            if ($tipo === 'grupo' && $papel !== 'admin') {
                return Json::erro($response, 'Apenas administradores podem criar grupos', 403);
            }

            if ($tipo === 'privada') {
                $outroId = (int) ($data['usuario_id'] ?? 0);
                if (!$outroId || $outroId === $userId) {
                    return Json::erro($response, 'Informe um usuário válido');
                }

                // Verifica se já existe conversa privada
                $pdo = getDbConnection();
                $check = $pdo->prepare("
                    SELECT c.id FROM conversas c
                    INNER JOIN participantes p1 ON p1.conversa_id = c.id AND p1.usuario_id = ?
                    INNER JOIN participantes p2 ON p2.conversa_id = c.id AND p2.usuario_id = ?
                    WHERE c.tipo = 'privada' LIMIT 1
                ");
                $check->execute([$userId, $outroId]);
                $existente = $check->fetch();

                if ($existente) {
                    return Json::json($response, ['id' => $existente['id'], 'ja_existe' => true]);
                }

                $conversaId = $this->conversaRepo->criar('privada');
                $this->conversaRepo->adicionarParticipante($conversaId, $userId);
                $this->conversaRepo->adicionarParticipante($conversaId, $outroId);

                $outro = $this->usuarioRepo->buscarPorId($outroId);

                return Json::json($response, [
                    'id' => $conversaId,
                    'tipo' => 'privada',
                    'nome' => $outro['nome'] ?? null,
                    'ja_existe' => false,
                ], 201);
            }

            // Grupo
            $nome = trim($data['nome'] ?? '');
            if (!$nome) {
                return Json::erro($response, 'Nome do grupo é obrigatório');
            }

            $conversaId = $this->conversaRepo->criar('grupo', $nome);
            $this->conversaRepo->adicionarParticipante($conversaId, $userId);

            $participantes = $data['participantes'] ?? '';
            if ($participantes) {
                $ids = array_filter(array_map('intval', explode(',', $participantes)));
                foreach ($ids as $pid) {
                    if ($pid === $userId) continue;
                    $this->conversaRepo->adicionarParticipante($conversaId, $pid);
                }
            }

            return Json::json($response, ['id' => $conversaId, 'tipo' => 'grupo', 'nome' => $nome], 201);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao criar conversa', 500);
        }
    }

    /** PATCH /api/conversas/{id} */
    public function editarConversa(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = $request->getAttribute('user_papel');
            $conversaId = (int) $args['id'];

            if ($papel !== 'admin') {
                return Json::erro($response, 'Apenas administradores podem editar grupos', 403);
            }

            $data = (array) $request->getParsedBody();
            $nome = trim($data['nome'] ?? '');

            if (!$nome) {
                return Json::erro($response, 'Nome é obrigatório');
            }

            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE conversas SET nome = ? WHERE id = ? AND tipo != 'privada'");
            $stmt->execute([$nome, $conversaId]);

            return Json::json($response, ['ok' => true, 'nome' => $nome]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao editar conversa', 500);
        }
    }

    /** DELETE /api/conversas/{id} */
    public function deletarConversa(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = $request->getAttribute('user_papel');
            $conversaId = (int) $args['id'];

            if ($papel !== 'admin') {
                return Json::erro($response, 'Apenas administradores podem excluir grupos', 403);
            }

            $conversa = $this->conversaRepo->buscarPorId($conversaId);

            if (!$conversa) {
                return Json::erro($response, 'Conversa não encontrada', 404);
            }

            if ($conversa['tipo'] === 'privada') {
                return Json::erro($response, 'Não é possível excluir conversas privadas');
            }

            $pdo = getDbConnection();
            $pdo->prepare("DELETE FROM conversas WHERE id = ?")->execute([$conversaId]);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao deletar conversa', 500);
        }
    }

    /** POST /api/conversas/{id}/lida */
    public function marcarComoLida(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $conversaId = (int) $args['id'];

            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE participantes SET ultima_leitura = NOW() WHERE conversa_id = ? AND usuario_id = ?");
            $stmt->execute([$conversaId, $userId]);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao marcar como lida', 500);
        }
    }

    /** GET /api/conversas/{id}/participantes */
    public function listarParticipantes(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            $conversaId = (int) $args['id'];

            // Verifica se tem acesso
            $pdo = getDbConnection();
            $check = $pdo->prepare("SELECT 1 FROM participantes WHERE conversa_id = ? AND usuario_id = ?");
            $check->execute([$conversaId, $userId]);
            if (!$check->fetch()) {
                return Json::erro($response, 'Acesso negado', 403);
            }

            $stmt = $pdo->prepare("
                SELECT u.id, u.nome, u.papel, s.nome AS setor
                FROM participantes p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN setores s ON s.id = u.setor_id
                WHERE p.conversa_id = ?
                ORDER BY u.nome ASC
            ");
            $stmt->execute([$conversaId]);

            return Json::json($response, $stmt->fetchAll());
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar participantes', 500);
        }
    }

    /** POST /api/conversas/{id}/participantes */
    public function adicionarParticipante(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = $request->getAttribute('user_papel');
            $conversaId = (int) $args['id'];
            $data = (array) $request->getParsedBody();
            $usuarioId = (int) ($data['usuario_id'] ?? 0);

            if ($papel !== 'admin') {
                return Json::erro($response, 'Apenas administradores podem adicionar participantes', 403);
            }

            if (!$usuarioId) {
                return Json::erro($response, 'usuario_id é obrigatório');
            }

            $this->conversaRepo->adicionarParticipante($conversaId, $usuarioId);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao adicionar participante', 500);
        }
    }

    /** DELETE /api/conversas/{id}/participantes/{uid} */
    public function removerParticipante(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = $request->getAttribute('user_papel');
            $myId = $request->getAttribute('user_id');
            $conversaId = (int) $args['id'];
            $usuarioId = (int) $args['uid'];

            if ($papel !== 'admin') {
                return Json::erro($response, 'Apenas administradores podem remover participantes', 403);
            }

            if ($usuarioId === $myId) {
                return Json::erro($response, 'Você não pode se remover do grupo');
            }

            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM participantes WHERE conversa_id = ? AND usuario_id = ?");
            $stmt->execute([$conversaId, $usuarioId]);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao remover participante', 500);
        }
    }
}