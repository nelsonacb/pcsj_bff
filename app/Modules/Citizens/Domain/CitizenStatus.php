<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Domain;


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