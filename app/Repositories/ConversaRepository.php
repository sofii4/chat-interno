<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;

class ConversaRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista conversas do usuário
     */
    public function listarPorUsuario(int $userId): array
    {
        $stmt = $this->pdo->prepare("
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma conversa por ID
     */
    public function buscarPorId(int $conversaId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, tipo, nome, criado_em
            FROM conversas
            WHERE id = ?
        ");
        $stmt->execute([$conversaId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria uma nova conversa
     */
    public function criar(string $tipo, ?string $nome = null): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO conversas (tipo, nome)
            VALUES (?, ?)
        ");
        $stmt->execute([$tipo, $nome]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Adiciona participante a uma conversa
     */
    public function adicionarParticipante(int $conversaId, int $userId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO participantes (conversa_id, usuario_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$conversaId, $userId]);
    }

    /**
     * Busca participantes de uma conversa
     */
    public function buscarParticipantes(int $conversaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT usuario_id FROM participantes
            WHERE conversa_id = ?
        ");
        $stmt->execute([$conversaId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map('intval', array_column($results, 'usuario_id'));
    }
}
