<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\Exceptions;

use DomainException;

final class InvalidCubanIdentityNumberException extends DomainException
{
    public static function wrongFormat(): self
    {
        return new self('El número de identidad debe tener exactamente 11 dígitos numéricos.');
    }

    public static function invalidCheckDigit(): self
    {
        return new self('El número de identidad tiene un dígito verificador inválido.');
    }

    public static function invalidBirthDate(): self
    {
        return new self('El número de identidad contiene una fecha de nacimiento inválida.');
    }
}