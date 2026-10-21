<?php

declare(strict_types=1);

namespace App\Modules\Requests\Domain;

enum DraftType: string
{
    case RetirementRequest = 'retirement_request';
    case EntityRequest     = 'entity_request';
}