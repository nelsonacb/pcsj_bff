<?php

declare(strict_types=1);

namespace App\Modules\Requests\Domain;

use App\Modules\Requests\Domain\Exceptions\InvalidPortalNumberException;

/**
 * Identificador público de una solicitud del portal.
 * Formato: PCSJ-{AAAA}-{8 dígitos}
 * Ejemplo: PCSJ-2026-00000001
 */
final readonly class PortalNumber
{
    private const PATTERN = '/^PCSJ-\d{4}-\d{8}$/';

    public function __construct(private string $value)
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw InvalidPortalNumberException::invalidFormat($value);
        }
    }

    public static function generate(int $year, int $sequence): self
    {
        return new self(sprintf('PCSJ-%d-%08d', $year, $sequence));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function year(): int
    {
        return (int) substr($this->value, 5, 4);
    }

    public function sequence(): int
    {
        return (int) substr($this->value, 10);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}