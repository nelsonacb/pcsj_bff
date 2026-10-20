<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Domain;

use App\Modules\Citizens\Domain\Exceptions\InvalidPasswordException;

final readonly class PasswordPolicy
{
    public const MIN_LENGTH = 12;

    public function __construct(private string $password)
    {
        $this->enforce();
    }

    private function enforce(): void
    {
        if (mb_strlen($this->password) < self::MIN_LENGTH) {
            throw InvalidPasswordException::tooShort(self::MIN_LENGTH);
        }

        if (!preg_match('/[A-Z]/', $this->password)) {
            throw InvalidPasswordException::missingUppercase();
        }

        if (!preg_match('/[a-z]/', $this->password)) {
            throw InvalidPasswordException::missingLowercase();
        }

        if (!preg_match('/\d/', $this->password)) {
            throw InvalidPasswordException::missingDigit();
        }

        if (!preg_match('/[^A-Za-z0-9]/', $this->password)) {
            throw InvalidPasswordException::missingSymbol();
        }
    }

    public function value(): string
    {
        return $this->password;
    }
}