<?php
declare(strict_types=1);
namespace App\DTOs;

class CreateUserDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly string $papel = 'usuario',
        public readonly ?int $setorId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: trim($data['nome'] ?? ''),
            email: trim($data['email'] ?? ''),
            senha: $data['senha'] ?? '',
            papel: $data['papel'] ?? 'usuario',
            setorId: isset($data['setor_id']) ? ((int) $data['setor_id'] ?: null) : null,
        );
    }
}
