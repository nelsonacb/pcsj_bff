<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain;

use App\Modules\Shared\Domain\Exceptions\InvalidCubanIdentityNumberException;

/**
 * Carnet de Identidad cubano (11 dígitos).
 *
 * Formato: YYMMDDNNNNC
 *  - YY:   año de nacimiento (2 dígitos)
 *  - MM:   mes
 *  - DD:   día
 *  - NNNN: consecutivo (el primer dígito codifica sexo y siglo)
 *  - C:    dígito verificador
 */
final readonly class CubanIdentityNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);

        if (!preg_match('/^\d{11}$/', $normalized)) {
            throw InvalidCubanIdentityNumberException::wrongFormat();
        }

        if (!self::isValidBirthDate($normalized)) {
            throw InvalidCubanIdentityNumberException::invalidBirthDate();
        }

        if (!self::hasValidCheckDigit($normalized)) {
            throw InvalidCubanIdentityNumberException::invalidCheckDigit();
        }

        $this->value = $normalized;
    }

    /**
     * Devuelve true si la CI es válida, false si no.
     * Útil en FormRequests: no lanza excepciones.
     */
    public static function isValid(string $value): bool
    {
        try {
            new self($value);
            return true;
        } catch (InvalidCubanIdentityNumberException) {
            return false;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function birthDate(): \DateTimeImmutable
    {
        $yy = (int) substr($this->value, 0, 2);
        $mm = (int) substr($this->value, 2, 2);
        $dd = (int) substr($this->value, 4, 2);

        $century = ((int) $this->value[6] < 5) ? 1900 : 2000;

        return new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $century + $yy, $mm, $dd));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function isValidBirthDate(string $ci): bool
    {
        $yy = (int) substr($ci, 0, 2);
        $mm = (int) substr($ci, 2, 2);
        $dd = (int) substr($ci, 4, 2);

        $century = ((int) $ci[6] < 5) ? 1900 : 2000;
        $year    = $century + $yy;

        return checkdate($mm, $dd, $year);
    }

    private static function hasValidCheckDigit(string $ci): bool
    {
        // @todo: reemplazar por la implementación exacta de inass_siss
        //        cuando tengas acceso al repo del SGP.

        $weights = [2, 3, 4, 5, 6, 7, 2, 3, 4, 5];
        $sum     = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $ci[$i] * $weights[$i];
        }

        $residue  = $sum % 11;
        $check    = 11 - $residue;
        $expected = ($check >= 10) ? 0 : $check;

        return $expected === (int) $ci[10];
    }
}