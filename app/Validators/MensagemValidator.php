<?php
declare(strict_types=1);
namespace App\Validators;

use App\Constants\ValidationRules;
use App\DTOs\CreateMessageDTO;
use App\Exceptions\ValidationException;

class MensagemValidator
{
    public function validateCreate(CreateMessageDTO $dto): void
    {
        if (!$dto->conversaId || !$dto->conteudo) {
            throw new ValidationException('conversa_id e conteudo são obrigatórios');
        }

        if (mb_strlen($dto->conteudo) > ValidationRules::MAX_MESSAGE_LENGTH) {
            throw new ValidationException(
                sprintf('Mensagem muito longa (máximo %d caracteres)', ValidationRules::MAX_MESSAGE_LENGTH)
            );
        }
    }
}
