<?php

declare(strict_types=1);

namespace App\Modules\Requests\Domain\Exceptions;

use DomainException;

final class InvalidPortalNumberException extends DomainException
{
    public static function invalidFormat(string $value): self
    {
        return new self("Invalid portal number: '{$value}'. Expected format: PCSJ-AAAA-XXXXXXXX.");
    }
}