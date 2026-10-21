<?php

declare(strict_types=1);

namespace App\Modules\Requests\Infrastructure\Models;

use App\Modules\Citizens\Infrastructure\Models\Citizen;
use App\Modules\Requests\Domain\DraftType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalDraft extends Model
{
    use SoftDeletes;

    protected $table = 'portal_drafts';

    protected $fillable = [
        'citizen_id',
        'type',
        'payload',
        'last_saved_at',
    ];

    protected $casts = [
        'payload'       => 'array',         
        'last_saved_at' => 'datetime',      
        'type'          => DraftType::class,  
    ];

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'citizen_id');
    }
}