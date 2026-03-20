<?php
declare(strict_types=1);
namespace App\Constants;

class ValidationRules
{
    public const MIN_PASSWORD_LENGTH = 6;
    public const MAX_MESSAGE_LENGTH = 5000;
    public const MESSAGES_PER_PAGE = 50;
    public const PASSWORD_COST = 12;
}
