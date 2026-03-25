<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\Response as Json;

class ChamadoController
{
    private const PRIORIDADES_VALIDAS = ['baixa', 'media', 'alta', 'critica'];

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

        if (!in_array($prioridade, self::PRIORIDADES_VALIDAS, true)) {
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

        $temResolvidoPor = $this->columnExists($pdo, 'chamados', 'resolvido_por');
        $joinResolvido = $temResolvidoPor ? 'LEFT JOIN usuarios r ON r.id = c.resolvido_por' : '';
        $selectResolvido = $temResolvidoPor ? 'r.nome AS resolvido_por_nome,' : 'NULL AS resolvido_por_nome,';

        if (in_array($papel, ['admin', 'ti'], true)) {
            $sql    = "SELECT c.*, u.nome AS usuario_nome,
                              a.nome AS atribuido_nome,
                              {$selectResolvido}
                              an.arquivo_path AS anexo_path,
                              an.arquivo_nome AS anexo_nome,
                              an.mime_type AS anexo_mime
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a
                       {$joinResolvido}
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
                      {$selectResolvido}
                      an.arquivo_path AS anexo_path,
                      an.arquivo_nome AS anexo_nome,
                      an.mime_type AS anexo_mime
                       FROM chamados c
                       INNER JOIN usuarios u ON u.id = c.usuario_id
                       LEFT JOIN usuarios a ON a.id = c.atribuido_a
                       {$joinResolvido}
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

        $statusValidos = ['aberto', 'classificado', 'em_andamento', 'resolvido', 'cancelado'];
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
            $papel = (string) $request->getAttribute('user_papel');
            if (!in_array($papel, ['admin', 'ti'], true)) {
                return Json::erro($response, 'Apenas TI/Admin pode classificar chamados', 403);
            }

            $id = (int) $args['id'];
            $data = (array) $request->getParsedBody();

            $prioridade   = (string) ($data['prioridade'] ?? 'media');
            $categoria    = trim((string) ($data['categoria'] ?? 'Geral'));
            $subcategoria = trim((string) ($data['subcategoria'] ?? 'Geral'));

            if (!in_array($prioridade, self::PRIORIDADES_VALIDAS, true)) {
                $prioridade = 'media';
            }
            if ($categoria === '') {
                $categoria = 'Geral';
            }
            if ($subcategoria === '') {
                $subcategoria = 'Geral';
            }

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

            $this->upsertTaxonomia($pdo, $categoria, $subcategoria);

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

    public function atualizarClassificacao(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = (string) $request->getAttribute('user_papel');
            if (!in_array($papel, ['admin', 'ti'], true)) {
                return Json::erro($response, 'Apenas TI/Admin pode editar classificacao', 403);
            }

            $id = (int) $args['id'];
            $data = (array) $request->getParsedBody();

            $prioridade = (string) ($data['prioridade'] ?? 'media');
            $categoria = trim((string) ($data['categoria'] ?? 'Geral'));
            $subcategoria = trim((string) ($data['subcategoria'] ?? 'Geral'));

            if (!in_array($prioridade, self::PRIORIDADES_VALIDAS, true)) {
                $prioridade = 'media';
            }
            if ($categoria === '') {
                $categoria = 'Geral';
            }
            if ($subcategoria === '') {
                $subcategoria = 'Geral';
            }

            $pdo = getDbConnection();

            if ($this->columnExists($pdo, 'chamados', 'categoria') && $this->columnExists($pdo, 'chamados', 'subcategoria')) {
                $stmt = $pdo->prepare("\n                    UPDATE chamados\n                    SET prioridade = ?,\n                        categoria = ?,\n                        subcategoria = ?\n                    WHERE id = ?\n                ");
                $stmt->execute([$prioridade, $categoria, $subcategoria, $id]);
                $this->upsertTaxonomia($pdo, $categoria, $subcategoria);
            } else {
                $stmt = $pdo->prepare('UPDATE chamados SET prioridade = ? WHERE id = ?');
                $stmt->execute([$prioridade, $id]);
            }

            return Json::json($response, ['status' => 'success', 'message' => 'Classificacao atualizada']);
        } catch (\Exception $e) {
            error_log('Erro ao atualizar classificacao: ' . $e->getMessage());
            return Json::erro($response, 'Erro ao atualizar classificacao', 500);
        }
    }

    public function listarTaxonomias(Request $request, Response $response): Response
    {
        try {
            $pdo = getDbConnection();

            if (!$this->tableExists($pdo, 'chamado_taxonomias')) {
                return Json::json($response, ['categorias' => $this->categoriasPadrao()]);
            }

            $stmt = $pdo->query("\n                SELECT categoria, subcategoria\n                FROM chamado_taxonomias\n                WHERE ativo = 1\n                ORDER BY categoria ASC, subcategoria ASC\n            ");

            $map = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $categoria = (string) $row['categoria'];
                $sub = (string) $row['subcategoria'];

                if (!isset($map[$categoria])) {
                    $map[$categoria] = [];
                }
                $map[$categoria][] = $sub;
            }

            return Json::json($response, ['categorias' => $map]);
        } catch (\Exception $e) {
            error_log('Erro ao listar taxonomias: ' . $e->getMessage());
            return Json::erro($response, 'Erro ao listar taxonomias', 500);
        }
    }

    public function salvarTaxonomia(Request $request, Response $response): Response
    {
        try {
            $papel = (string) $request->getAttribute('user_papel');
            if (!in_array($papel, ['admin', 'ti'], true)) {
                return Json::erro($response, 'Apenas TI/Admin pode gerenciar categorias', 403);
            }

            $data = (array) $request->getParsedBody();
            $categoria = trim((string) ($data['categoria'] ?? ''));
            $subcategoria = trim((string) ($data['subcategoria'] ?? ''));

            if ($categoria === '' || $subcategoria === '') {
                return Json::erro($response, 'categoria e subcategoria sao obrigatorias');
            }

            $pdo = getDbConnection();
            if (!$this->tableExists($pdo, 'chamado_taxonomias')) {
                return Json::erro($response, 'Tabela de taxonomias indisponivel. Rode a migracao.', 409);
            }
            $this->upsertTaxonomia($pdo, $categoria, $subcategoria);

            return Json::json($response, ['ok' => true], 201);
        } catch (\Exception $e) {
            error_log('Erro ao salvar taxonomia: ' . $e->getMessage());
            return Json::erro($response, 'Erro ao salvar taxonomia', 500);
        }
    }

    public function removerTaxonomia(Request $request, Response $response, array $args): Response
    {
        try {
            $papel = (string) $request->getAttribute('user_papel');
            if (!in_array($papel, ['admin', 'ti'], true)) {
                return Json::erro($response, 'Apenas TI/Admin pode gerenciar categorias', 403);
            }

            $id = (int) $args['id'];
            if ($id <= 0) {
                return Json::erro($response, 'ID invalido');
            }

            $pdo = getDbConnection();
            if (!$this->tableExists($pdo, 'chamado_taxonomias')) {
                return Json::erro($response, 'Tabela de taxonomias indisponivel. Rode a migracao.', 409);
            }
            $stmt = $pdo->prepare('UPDATE chamado_taxonomias SET ativo = 0 WHERE id = ?');
            $stmt->execute([$id]);

            return Json::json($response, ['ok' => true]);
        } catch (\Exception $e) {
            error_log('Erro ao remover taxonomia: ' . $e->getMessage());
            return Json::erro($response, 'Erro ao remover taxonomia', 500);
        }
    }

    public function listarTaxonomiasDetalhe(Request $request, Response $response): Response
    {
        try {
            $pdo = getDbConnection();
            if (!$this->tableExists($pdo, 'chamado_taxonomias')) {
                return Json::json($response, []);
            }
            $stmt = $pdo->query("\n                SELECT id, categoria, subcategoria\n                FROM chamado_taxonomias\n                WHERE ativo = 1\n                ORDER BY categoria ASC, subcategoria ASC\n            ");

            return Json::json($response, $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\Exception $e) {
            error_log('Erro ao listar taxonomias detalhadas: ' . $e->getMessage());
            return Json::erro($response, 'Erro ao listar taxonomias', 500);
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
            if ($this->columnExists($pdo, 'chamados', 'resolvido_por')) {
                $stmtUp = $pdo->prepare("UPDATE chamados SET status = 'resolvido', resolvido_por = ? WHERE id = ?");
                $stmtUp->execute([$finalizadorId, $id]);
            } else {
                $stmtUp = $pdo->prepare("UPDATE chamados SET status = 'resolvido' WHERE id = ?");
                $stmtUp->execute([$id]);
            }

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

    private function upsertTaxonomia(\PDO $pdo, string $categoria, string $subcategoria): void
    {
        if (!$this->tableExists($pdo, 'chamado_taxonomias')) {
            return;
        }

        $stmt = $pdo->prepare("\n            INSERT INTO chamado_taxonomias (categoria, subcategoria, ativo)\n            VALUES (?, ?, 1)\n            ON DUPLICATE KEY UPDATE ativo = 1\n        ");
        $stmt->execute([$categoria, $subcategoria]);
    }

    private function tableExists(\PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare("\n            SELECT COUNT(*)\n            FROM information_schema.TABLES\n            WHERE TABLE_SCHEMA = DATABASE()\n              AND TABLE_NAME = ?\n        ");
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(\PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare("\n            SELECT COUNT(*)\n            FROM information_schema.COLUMNS\n            WHERE TABLE_SCHEMA = DATABASE()\n              AND TABLE_NAME = ?\n              AND COLUMN_NAME = ?\n        ");
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function categoriasPadrao(): array
    {
        return [
            'ERP' => ['Financeiro', 'Fiscal', 'Contabilidade', 'Vendas', 'Estoque'],
            'Infraestrutura' => ['Servidor', 'Backup', 'Cloud', 'Banco de Dados'],
            'Engenharia' => ['AutoCAD', 'Solidworks', 'Revisão Técnica'],
            'Redes' => ['Wi-Fi', 'Cabeamento', 'VPN'],
            'Segurança' => ['Antivírus', 'Firewall', 'Câmeras'],
            'Hardware' => ['Desktop/Notebook', 'Impressora', 'Periféricos'],
            'Acessos' => ['Reset de Senha', 'Novo Usuário', 'Permissões'],
        ];
    }
}
