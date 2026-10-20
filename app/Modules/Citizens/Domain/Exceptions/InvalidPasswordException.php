<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Domain\Exceptions;

use DomainException;

final class InvalidPasswordException extends DomainException
{
    public static function tooShort(int $min): self
    {
        return new self("La contraseña debe tener al menos {$min} caracteres.");
    }

    public static function missingUppercase(): self
    {
        return new self('La contraseña debe incluir al menos una letra mayúscula.');
    }

    public static function missingLowercase(): self
    {
        return new self('La contraseña debe incluir al menos una letra minúscula.');
    }

    public static function missingDigit(): self
    {
        return new self('La contraseña debe incluir al menos un dígito.');
    }

    public static function missingSymbol(): self
    {
        return new self('La contraseña debe incluir al menos un símbolo.');
    }
}