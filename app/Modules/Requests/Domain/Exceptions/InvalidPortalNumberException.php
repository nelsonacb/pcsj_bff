<?php

declare(strict_types=1);

namespace App\Modules\Requests\Domain\Exceptions;

use DomainException;

final class InvalidPortalNumberException extends DomainException
{
    public static function invalidFormat(string $value): self
    {
        return new self("Número de portal inválido: '{$value}'. Formato esperado: PCSJ-AAAA-XXXXXXXX.");
    }
}