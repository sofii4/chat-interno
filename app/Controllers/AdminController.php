<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;
use App\Repositories\UsuarioRepository;
use App\Repositories\SetorRepository;
use App\Validators\UsuarioValidator;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Exceptions\ValidationException;

class AdminController
{
    public function __construct(
        private UsuarioRepository $usuarioRepo,
        private SetorRepository $setorRepo,
        private UsuarioValidator $usuarioValidator,
    ) {}

    // ──────────────────────────────────────────
    // USUÁRIOS
    // ──────────────────────────────────────────

    /** GET /api/admin/usuarios */
    public function listarUsuarios(Request $request, Response $response): Response
    {
        try {
            $usuarios = $this->usuarioRepo->listarTodos();
            return Json::json($response, $usuarios);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar usuários', 500);
        }
    }

    /** POST /api/admin/usuarios */
    public function criarUsuario(Request $request, Response $response): Response
    {
        try {
            $data = (array) $request->getParsedBody();
            $dto = CreateUserDTO::fromArray($data);

            $this->usuarioValidator->validateCreate($dto);

            $id = $this->usuarioRepo->criar($dto);

            return Json::json($response, [
                'id' => $id,
                'nome' => $dto->nome,
                'email' => $dto->email,
                'papel' => $dto->papel,
            ], 201);
        } catch (ValidationException $e) {
            return Json::erro($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao criar usuário', 500);
        }
    }

    /** PATCH /api/admin/usuarios/{id} */
    public function atualizarUsuario(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int) $args['id'];
            $data = (array) $request->getParsedBody();
            $dto = UpdateUserDTO::fromArray($data);

            $this->usuarioValidator->validateUpdate($dto);

            $this->usuarioRepo->atualizar($userId, $dto);

            return Json::json($response, ['ok' => true]);
        } catch (ValidationException $e) {
            return Json::erro($response, $e->getMessage(), 400);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao atualizar usuário', 500);
        }
    }

    /** DELETE /api/admin/usuarios/{id} */
    public function desativarUsuario(Request $request, Response $response, array $args): Response
    {
        try {
            $userId = (int) $args['id'];
            $myId = $request->getAttribute('user_id');

            if ($userId === $myId) {
                return Json::erro($response, 'Você não pode desativar sua própria conta');
            }

            $this->usuarioRepo->desativar($userId);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao desativar usuário', 500);
        }
    }

    // ──────────────────────────────────────────
    // SETORES
    // ──────────────────────────────────────────

    /** GET /api/admin/setores */
    public function listarSetores(Request $request, Response $response): Response
    {
        try {
            $setores = $this->setorRepo->listarTodos();
            return Json::json($response, $setores);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao listar setores', 500);
        }
    }

    /** POST /api/admin/setores */
    public function criarSetor(Request $request, Response $response): Response
    {
        try {
            $data = (array) $request->getParsedBody();
            $nome = trim($data['nome'] ?? '');
            $descricao = trim($data['descricao'] ?? '');

            if (!$nome) {
                return Json::erro($response, 'Nome do setor é obrigatório');
            }

            $id = $this->setorRepo->criar($nome, $descricao);

            return Json::json($response, [
                'id' => $id,
                'nome' => $nome,
            ], 201);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao criar setor', 500);
        }
    }

    /** DELETE /api/admin/setores/{id} */
    public function deletarSetor(Request $request, Response $response, array $args): Response
    {
        try {
            $setorId = (int) $args['id'];

            if ($this->setorRepo->temUsuariosAtivos($setorId)) {
                return Json::erro(
                    $response,
                    'Setor possui usuários ativos. Mova-os antes de deletar.'
                );
            }

            $this->setorRepo->deletar($setorId);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            return Json::erro($response, 'Erro ao deletar setor', 500);
        }
    }
}
