<?php
declare(strict_types=1);
namespace App\Exceptions;

class UnauthorizedException extends \Exception
{
    public function __construct(string $message = 'Acesso não autorizado', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
