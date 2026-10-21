<?php

declare(strict_types=1);

namespace App\Modules\Requests\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalAttachment extends Model
{
    protected $table = 'portal_attachments';

    protected $fillable = [
        'draft_id',
        'request_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'storage_path',
        'checksum',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function draft(): BelongsTo
    {
        return $this->belongsTo(PortalDraft::class, 'draft_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(PortalRequest::class, 'request_id');
    }

    /**
     * Tamaño legible para humanos: "2.4 MB", "340 KB".
     */
    public function humanSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}