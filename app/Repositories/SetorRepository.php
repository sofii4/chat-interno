<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;

class SetorRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista todos os setores com contagem de usuários
     */
    public function listarTodos(): array
    {
        $stmt = $this->pdo->query("
            SELECT s.id, s.nome, s.descricao,
                   COUNT(u.id) AS total_usuarios
            FROM setores s
            LEFT JOIN usuarios u ON u.setor_id = s.id AND u.ativo = 1
            GROUP BY s.id
            ORDER BY s.nome ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um setor por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, descricao FROM setores WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria um novo setor
     */
    public function criar(string $nome, string $descricao = ''): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO setores (nome, descricao) VALUES (?, ?)");
        $stmt->execute([$nome, $descricao]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Verifica se setor tem usuários ativos
     */
    public function temUsuariosAtivos(int $setorId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as total FROM usuarios
            WHERE setor_id = ? AND ativo = 1
        ");
        $stmt->execute([$setorId]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    /**
     * Deleta um setor
     */
    public function deletar(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM setores WHERE id = ?");
        $stmt->execute([$id]);
    }
}
