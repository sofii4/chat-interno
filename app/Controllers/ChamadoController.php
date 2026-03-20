<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;

class ChamadoController
{
    // POST /api/chamados
    public function criar(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $data   = (array) $request->getParsedBody();
        $files  = $request->getUploadedFiles();

        $titulo     = trim($data['titulo'] ?? '');
        $descricao  = trim($data['descricao'] ?? '');
        $prioridade = $data['prioridade'] ?? 'media';

        // Validações
        if (!$titulo) {
            return Json::erro($response, 'Título é obrigatório');
        }

        $prioridadesValidas = ['baixa', 'media', 'alta', 'critica'];
        if (!in_array($prioridade, $prioridadesValidas, true)) {
            $prioridade = 'media';
        }

        $pdo = getDbConnection();

        // Salva o chamado
        $stmt = $pdo->prepare("
            INSERT INTO chamados (usuario_id, titulo, descricao_rich, prioridade, status)
            VALUES (?, ?, ?, ?, 'aberto')
        ");
        $stmt->execute([$userId, $titulo, $descricao, $prioridade]);
        $chamadoId = (int) $pdo->lastInsertId();

        // Processa anexos se existirem
        $anexosSalvos = [];
        if (!empty($files['anexos'])) {
            $anexos = $files['anexos'];
            // Normaliza para array (pode vir como objeto único)
            if (!is_array($anexos)) $anexos = [$anexos];

            foreach ($anexos as $arquivo) {
                if ($arquivo->getError() !== UPLOAD_ERR_OK) continue;

                try {
                    $path = $this->salvarArquivo($arquivo, $chamadoId);

                    $stmtAnexo = $pdo->prepare("
                        INSERT INTO chamado_anexos (chamado_id, arquivo_path, arquivo_nome, mime_type, tamanho_bytes)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmtAnexo->execute([
                        $chamadoId,
                        $path,
                        $arquivo->getClientFilename(),
                        $arquivo->getClientMediaType(),
                        $arquivo->getSize(),
                    ]);

                    $anexosSalvos[] = $arquivo->getClientFilename();
                } catch (\Exception $e) {
                    error_log('Erro ao salvar anexo: ' . $e->getMessage());
                }
            }
        }

        // Busca o chamado completo para retornar
        $novo = $pdo->prepare("
            SELECT c.*, u.nome AS usuario_nome
            FROM chamados c
            INNER JOIN usuarios u ON u.id = c.usuario_id
            WHERE c.id = ?
        ");
        $novo->execute([$chamadoId]);

        return Json::json($response, [
            'chamado' => $novo->fetch(),
            'anexos'  => $anexosSalvos,
        ], 201);
    }

    // GET /api/chamados
    public function listar(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $papel  = $request->getAttribute('user_papel');
        $params = $request->getQueryParams();
        $status = $params['status'] ?? null;

        $pdo = getDbConnection();

        // Admin e TI veem todos, usuário comum vê só os seus
        if (in_array($papel, ['admin', 'ti'], true)) {
            $sql    = "SELECT c.*, u.nome AS usuario_nome,
                              a.nome AS atribuido_nome
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a";
            $params = [];

            if ($status) {
                $sql .= " WHERE c.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY
                        FIELD(c.prioridade, 'critica','alta','media','baixa'),
                        c.criado_em DESC";
        } else {
            $sql    = "SELECT c.*, u.nome AS usuario_nome,
                              a.nome AS atribuido_nome
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a
                       WHERE c.usuario_id = ?";
            $params = [$userId];

            if ($status) {
                $sql .= " AND c.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY c.criado_em DESC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return Json::json($response, $stmt->fetchAll());
    }

    // PATCH /api/chamados/{id}/status
    public function atualizarStatus(Request $request, Response $response, array $args): Response
    {
        $papel = $request->getAttribute('user_papel');

        if (!in_array($papel, ['admin', 'ti'], true)) {
            return Json::erro($response, 'Apenas TI pode atualizar chamados', 403);
        }

        $chamadoId = (int) $args['id'];
        $data      = (array) $request->getParsedBody();
        $novoStatus = $data['status'] ?? '';

        $statusValidos = ['aberto', 'em_andamento', 'resolvido', 'cancelado'];
        if (!in_array($novoStatus, $statusValidos, true)) {
            return Json::erro($response, 'Status inválido');
        }

        $pdo  = getDbConnection();
        $stmt = $pdo->prepare("UPDATE chamados SET status = ? WHERE id = ?");
        $stmt->execute([$novoStatus, $chamadoId]);

        return Json::json($response, ['ok' => true, 'status' => $novoStatus]);
    }

    // Salva arquivo de forma segura
    private function salvarArquivo($arquivo, int $chamadoId): string
    {
        $mimesPermitidos = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $tamanhoMax = (int) ($_ENV['UPLOAD_MAX_SIZE'] ?? 10485760);

        if ($arquivo->getSize() > $tamanhoMax) {
            throw new \RuntimeException('Arquivo muito grande (máximo 10MB)');
        }

        // Valida MIME real pelo conteúdo
        $tmpPath  = $arquivo->getStream()->getMetadata('uri');
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($tmpPath);

        if (!in_array($mimeReal, $mimesPermitidos, true)) {
            throw new \RuntimeException("Tipo não permitido: {$mimeReal}");
        }

        $ext      = strtolower(pathinfo($arquivo->getClientFilename(), PATHINFO_EXTENSION));
        $novoNome = bin2hex(random_bytes(16)) . '.' . $ext;
        $destDir  = __DIR__ . '/../../public/uploads/chamados/' . $chamadoId . '/';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0750, true);
        }

        $arquivo->moveTo($destDir . $novoNome);

        return 'chamados/' . $chamadoId . '/' . $novoNome;
    }
}
