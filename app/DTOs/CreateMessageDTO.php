<?php
declare(strict_types=1);
namespace App\DTOs;

class CreateMessageDTO
{
    public function __construct(
        public readonly int $conversaId,
        public readonly string $conteudo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            conversaId: (int) ($data['conversa_id'] ?? 0),
            conteudo: trim($data['conteudo'] ?? ''),
        );
    }
}
