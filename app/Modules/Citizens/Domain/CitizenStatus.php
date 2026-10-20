<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Domain;

/**
 * Estado de una cuenta ciudadana en el portal.
 */
enum CitizenStatus: string
{
    case Unverified  = 'unverified';  
    case Active      = 'active';      
    case Locked      = 'locked';      
    case Deactivated = 'deactivated'; 

    public function canLogin(): bool
    {
        return $this === self::Active;
    }

    public function isTerminal(): bool
    {
        return $this === self::Deactivated;
    }
}