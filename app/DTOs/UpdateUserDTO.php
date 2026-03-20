<?php
declare(strict_types=1);
namespace App\DTOs;

class UpdateUserDTO
{
    public function __construct(
        public readonly ?string $nome = null,
        public readonly ?string $papel = null,
        public readonly ?int $setorId = null,
        public readonly ?int $ativo = null,
        public readonly ?string $senha = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: !empty($data['nome']) ? trim($data['nome']) : null,
            papel: $data['papel'] ?? null,
            setorId: isset($data['setor_id']) ? ((int) $data['setor_id'] ?: null) : null,
            ativo: isset($data['ativo']) ? (int) $data['ativo'] : null,
            senha: $data['senha'] ?? null,
        );
    }

    public function isEmpty(): bool
    {
        return $this->nome === null &&
               $this->papel === null &&
               $this->setorId === null &&
               $this->ativo === null &&
               $this->senha === null;
    }
}
