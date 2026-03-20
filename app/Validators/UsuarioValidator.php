<?php
declare(strict_types=1);
namespace App\Validators;

use App\Constants\UserRole;
use App\Constants\ValidationRules;
use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Exceptions\ValidationException;

class UsuarioValidator
{
    public function validateCreate(CreateUserDTO $dto): void
    {
        if (!$dto->nome || !$dto->email || !$dto->senha) {
            throw new ValidationException('Nome, e-mail e senha são obrigatórios');
        }

        $this->validateEmail($dto->email);
        $this->validatePassword($dto->senha);
        $this->validateRole($dto->papel);
    }

    public function validateUpdate(UpdateUserDTO $dto): void
    {
        if ($dto->isEmpty()) {
            throw new ValidationException('Nenhum campo para atualizar');
        }

        if ($dto->email !== null) {
            $this->validateEmail($dto->email);
        }

        if ($dto->senha !== null) {
            $this->validatePassword($dto->senha);
        }

        if ($dto->papel !== null) {
            $this->validateRole($dto->papel);
        }
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('E-mail inválido');
        }
    }

    private function validatePassword(string $senha): void
    {
        if (strlen($senha) < ValidationRules::MIN_PASSWORD_LENGTH) {
            throw new ValidationException(
                sprintf('Senha deve ter ao menos %d caracteres', ValidationRules::MIN_PASSWORD_LENGTH)
            );
        }
    }

    private function validateRole(string $papel): void
    {
        if (!UserRole::isValid($papel)) {
            throw new ValidationException('Papel de usuário inválido');
        }
    }
}
