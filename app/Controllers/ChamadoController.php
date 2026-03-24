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
        $anexosErros = [];

        $anexos = $files['anexos'] ?? $files['anexos[]'] ?? null;
        if (!empty($anexos)) {
            if (!is_array($anexos)) $anexos = [$anexos];

            foreach ($anexos as $arquivo) {
                $nomeArquivo = method_exists($arquivo, 'getClientFilename') ? (string) $arquivo->getClientFilename() : 'arquivo';

                if ($arquivo->getError() !== UPLOAD_ERR_OK) {
                    $anexosErros[] = [
                        'arquivo' => $nomeArquivo,
                        'erro' => 'Falha no upload (codigo ' . $arquivo->getError() . ')',
                    ];
                    continue;
                }

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
                    $anexosErros[] = [
                        'arquivo' => $nomeArquivo,
                        'erro' => $e->getMessage(),
                    ];
                }
            }
        }

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
            'anexo_erros' => $anexosErros,
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

        $joinAnexo = "
            LEFT JOIN (
                SELECT ca.chamado_id, ca.arquivo_path, ca.arquivo_nome, ca.mime_type
                FROM chamado_anexos ca
                INNER JOIN (
                    SELECT chamado_id, MIN(id) AS primeiro_id
                    FROM chamado_anexos
                    GROUP BY chamado_id
                ) primeiro ON primeiro.primeiro_id = ca.id
            ) an ON an.chamado_id = c.id
        ";

        if (in_array($papel, ['admin', 'ti'], true)) {
            $sql    = "SELECT c.*, u.nome AS usuario_nome,
                              a.nome AS atribuido_nome,
                              an.arquivo_path AS anexo_path,
                              an.arquivo_nome AS anexo_nome,
                              an.mime_type AS anexo_mime
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a
                       {$joinAnexo}";
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
                      a.nome AS atribuido_nome,
                      an.arquivo_path AS anexo_path,
                      an.arquivo_nome AS anexo_nome,
                      an.mime_type AS anexo_mime
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a
                  {$joinAnexo}
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
            'image/jpg', 'image/heic', 'image/heif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $tamanhoMax = (int) ($_ENV['UPLOAD_MAX_SIZE'] ?? 10485760);

        if ($arquivo->getSize() > $tamanhoMax) {
            throw new \RuntimeException('Arquivo muito grande (máximo 10MB)');
        }

        $tmpPath  = $arquivo->getStream()->getMetadata('uri');
        if (!$tmpPath || !is_file($tmpPath)) {
            throw new \RuntimeException('Arquivo temporario invalido para upload');
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($tmpPath);

        if (!in_array($mimeReal, $mimesPermitidos, true)) {
            throw new \RuntimeException("Tipo não permitido: {$mimeReal}");
        }

        $ext      = strtolower(pathinfo($arquivo->getClientFilename(), PATHINFO_EXTENSION));
        if ($ext === '') {
            $mapaExtensao = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/heic' => 'heic',
                'image/heif' => 'heif',
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            ];
            $ext = $mapaExtensao[$mimeReal] ?? 'bin';
        }

        $novoNome = bin2hex(random_bytes(16)) . '.' . $ext;
        $destDir  = __DIR__ . '/../../public/uploads/chamados/' . $chamadoId . '/';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $caminhoCompleto = $destDir . $novoNome;
        $arquivo->moveTo($caminhoCompleto);

        return 'chamados/' . $chamadoId . '/' . $novoNome;
    }

    public function classificar(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $data = (array) $request->getParsedBody();

            $prioridade   = $data['prioridade'] ?? 'media';
            $categoria    = $data['categoria'] ?? 'Geral';
            $subcategoria = $data['subcategoria'] ?? 'Geral';

            $pdo = getDbConnection();

            $stmt = $pdo->prepare("
                UPDATE chamados 
                SET prioridade = ?, 
                    categoria = ?, 
                    subcategoria = ?, 
                    status = 'classificado' 
                WHERE id = ?
            ");
            
            $stmt->execute([$prioridade, $categoria, $subcategoria, $id]);

            // Usando retorno nativo blindado
            $payload = json_encode(['status' => 'success', 'message' => 'Classificado com sucesso']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            error_log("Erro na classificação: " . $e->getMessage());
            $payload = json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function finalizar(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $finalizadorId = (int) $request->getAttribute('user_id');
            $papel = (string) $request->getAttribute('user_papel');

            if (!in_array($papel, ['admin', 'ti'], true)) {
                return Json::erro($response, 'Apenas TI pode finalizar chamados', 403);
            }

            $pdo = getDbConnection();

            // 1. Pega informações
            $stmtBusca = $pdo->prepare("SELECT titulo, usuario_id FROM chamados WHERE id = ?");
            $stmtBusca->execute([$id]);
            $chamado = $stmtBusca->fetch(\PDO::FETCH_ASSOC);

            if (!$chamado) {
                throw new \Exception("Chamado não encontrado.");
            }

            // 2. Atualiza o status
            $stmtUp = $pdo->prepare("UPDATE chamados SET status = 'resolvido' WHERE id = ?");
            $stmtUp->execute([$id]);

            // 3. Bloco isolado para a mensagem (se falhar, o chamado fecha mesmo assim)
            try {
                $solicitanteId = (int) $chamado['usuario_id'];
                if ($solicitanteId !== $finalizadorId) {
                    $conversaId = $this->obterOuCriarConversaPrivada($pdo, $finalizadorId, $solicitanteId);
                    $mensagemAutomatica = "Chamado #{$id} (\"{$chamado['titulo']}\") foi finalizado pela equipe de TI.";
                    $stmtMsg = $pdo->prepare("INSERT INTO mensagens (conversa_id, usuario_id, conteudo, criado_em) VALUES (?, ?, ?, NOW())");
                    $stmtMsg->execute([$conversaId, $finalizadorId, $mensagemAutomatica]);
                }
            } catch (\Exception $eMsg) {
                error_log("Aviso: Falha ao enviar mensagem de chat, mas chamado foi fechado. Erro: " . $eMsg->getMessage());
            }

            $payload = json_encode(['status' => 'success']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            
        } catch (\Exception $e) {
            error_log("Erro ao finalizar: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    private function obterOuCriarConversaPrivada(\PDO $pdo, int $usuarioA, int $usuarioB): int
    {
        $check = $pdo->prepare("\n            SELECT c.id FROM conversas c\n            INNER JOIN participantes p1 ON p1.conversa_id = c.id AND p1.usuario_id = ?\n            INNER JOIN participantes p2 ON p2.conversa_id = c.id AND p2.usuario_id = ?\n            WHERE c.tipo = 'privada'\n            LIMIT 1\n        ");
        $check->execute([$usuarioA, $usuarioB]);
        $existente = $check->fetch(\PDO::FETCH_ASSOC);

        if ($existente && isset($existente['id'])) {
            return (int) $existente['id'];
        }

        $stmt = $pdo->prepare("INSERT INTO conversas (tipo, nome, criado_por) VALUES ('privada', NULL, ?)");
        $stmt->execute([$usuarioA]);
        $conversaId = (int) $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")->execute([$conversaId, $usuarioA]);
        $pdo->prepare("INSERT INTO participantes (conversa_id, usuario_id) VALUES (?, ?)")->execute([$conversaId, $usuarioB]);

        return $conversaId;
    }
}
