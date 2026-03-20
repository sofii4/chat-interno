<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;
use App\Constants\ValidationRules;
use App\DTOs\CreateMessageDTO;
use App\Exceptions\UnauthorizedException;

class MensagemRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista mensagens de uma conversa com paginação
     */
    public function listarPorConversa(
        int $conversaId,
        int $userId,
        int $pagina = 1,
        int $porPagina = ValidationRules::MESSAGES_PER_PAGE
    ): array {
        // Verifica se usuário tem acesso à conversa
        if (!$this->usuarioTemAcesso($conversaId, $userId)) {
            throw new UnauthorizedException('Acesso negado a esta conversa');
        }

        $offset = ($pagina - 1) * $porPagina;

        $stmt = $this->pdo->prepare("
            SELECT m.id, m.conteudo, m.arquivo_path, m.arquivo_nome, m.criado_em,
                   u.id AS usuario_id, u.nome AS usuario_nome
            FROM mensagens m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.conversa_id = ?
            ORDER BY m.criado_em DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$conversaId, $porPagina, $offset]);
        $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retorna em ordem cronológica (mais antiga primeiro)
        return array_reverse($mensagens);
    }

    /**
     * Cria uma nova mensagem
     */
    public function criar(int $conversaId, int $userId, CreateMessageDTO $dto): int
    {
        // Verifica se usuário tem acesso à conversa
        if (!$this->usuarioTemAcesso($conversaId, $userId)) {
            throw new UnauthorizedException('Acesso negado a esta conversa');
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO mensagens (conversa_id, usuario_id, conteudo)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$conversaId, $userId, $dto->conteudo]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Busca uma mensagem completa por ID
     */
    public function buscarPorId(int $msgId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.conteudo, m.criado_em,
                   u.id AS usuario_id, u.nome AS usuario_nome
            FROM mensagens m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.id = ?
        ");
        $stmt->execute([$msgId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Verifica se usuário tem acesso à conversa
     */
    private function usuarioTemAcesso(int $conversaId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1 FROM participantes
            WHERE conversa_id = ? AND usuario_id = ?
        ");
        $stmt->execute([$conversaId, $userId]);
        return (bool) $stmt->fetch();
    }
}
