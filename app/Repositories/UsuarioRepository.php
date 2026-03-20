<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;
use App\Constants\UserRole;
use App\Constants\ValidationRules;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Exceptions\ValidationException;

class UsuarioRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Lista todos os usuários com informações de setor
     */
    public function listarTodos(): array
    {
        $stmt = $this->pdo->query("
            SELECT u.id, u.nome, u.email, u.papel, u.ativo,
                   u.criado_em, s.nome AS setor
            FROM usuarios u
            LEFT JOIN setores s ON s.id = u.setor_id
            ORDER BY u.nome ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista usuários ativos (exceto o filtrado)
     */
    public function listarAtivos(?int $excluirId = null): array
    {
        if ($excluirId === null) {
            $stmt = $this->pdo->query("
                SELECT u.id, u.nome, u.papel, s.nome AS setor
                FROM usuarios u
                LEFT JOIN setores s ON s.id = u.setor_id
                WHERE u.ativo = 1
                ORDER BY u.nome ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->pdo->prepare("
            SELECT u.id, u.nome, u.papel, s.nome AS setor
            FROM usuarios u
            LEFT JOIN setores s ON s.id = u.setor_id
            WHERE u.ativo = 1 AND u.id != ?
            ORDER BY u.nome ASC
        ");
        $stmt->execute([$excluirId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário por ID
     */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.nome, u.email, u.papel, u.ativo,
                   u.criado_em, u.setor_id, s.nome AS setor
            FROM usuarios u
            LEFT JOIN setores s ON s.id = u.setor_id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Busca um usuário por e-mail
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria um novo usuário
     */
    public function criar(CreateUserDTO $dto): int
    {
        // Verifica e-mail duplicado
        if ($this->buscarPorEmail($dto->email)) {
            throw new ValidationException('E-mail já cadastrado');
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nome, email, senha_hash, setor_id, papel)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $dto->nome,
            $dto->email,
            $this->hashPassword($dto->senha),
            $dto->setorId,
            $dto->papel,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Atualiza um usuário existente
     */
    public function atualizar(int $id, UpdateUserDTO $dto): void
    {
        $campos = [];
        $valores = [];

        if ($dto->nome !== null) {
            $campos[] = 'nome = ?';
            $valores[] = $dto->nome;
        }

        if ($dto->papel !== null) {
            $campos[] = 'papel = ?';
            $valores[] = $dto->papel;
        }

        if ($dto->setorId !== null) {
            $campos[] = 'setor_id = ?';
            $valores[] = $dto->setorId;
        }

        if ($dto->ativo !== null) {
            $campos[] = 'ativo = ?';
            $valores[] = $dto->ativo;
        }

        if ($dto->senha !== null) {
            $campos[] = 'senha_hash = ?';
            $valores[] = $this->hashPassword($dto->senha);
        }

        if (empty($campos)) {
            return;
        }

        $valores[] = $id;
        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($valores);
    }

    /**
     * Desativa um usuário (soft delete)
     */
    public function desativar(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Hash a senha com BCRYPT
     */
    private function hashPassword(string $senha): string
    {
        return password_hash($senha, PASSWORD_BCRYPT, ['cost' => ValidationRules::PASSWORD_COST]);
    }
}
