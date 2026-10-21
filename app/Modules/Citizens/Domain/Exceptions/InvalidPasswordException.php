<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Domain\Exceptions;

use DomainException;

final class InvalidPasswordException extends DomainException
{
    public static function tooShort(int $min): self
    {
        return new self("The password must be at least {$min} characters long.");
    }

    public static function missingUppercase(): self
    {
        return new self('The password must include at least one uppercase letter.');
    }

    public static function missingLowercase(): self
    {
        return new self('The password must include at least one lowercase letter.');
    }

    public static function missingDigit(): self
    {
        return new self('The password must include at least one digit.');
    }

    public static function missingSymbol(): self
    {
        return new self('The password must include at least one symbol.');
    }
}