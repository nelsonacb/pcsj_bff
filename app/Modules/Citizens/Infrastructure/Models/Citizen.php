<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Infrastructure\Models;

use App\Modules\Citizens\Domain\CitizenStatus;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Citizens\Presentation\Notifications\ResetPasswordNotification;
use App\Modules\Citizens\Presentation\Notifications\VerifyEmailNotification;

class Citizen extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
    use HasApiTokens;   
    use HasFactory;     
    use Notifiable;     
    use SoftDeletes;    
    use CanResetPassword;

    protected $table = 'citizens';

    protected $fillable = [
        'identity_number',
        'email',
        'password_hash',
        'full_name',
        'phone',
        'person_id_sgp',
        'status',
    ];

    protected $hidden = [
        'password_hash',  
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until'      => 'datetime',
        'status'            => CitizenStatus::class,
    ];

 
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function setPassword(string $plainPassword): void
    {
        $this->password_hash = Hash::make($plainPassword);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return Hash::check($plainPassword, $this->password_hash);
    }

    public function drafts(): HasMany
    {
        return $this->hasMany(\App\Modules\Requests\Infrastructure\Models\PortalDraft::class, 'citizen_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(\App\Modules\Requests\Infrastructure\Models\PortalAttachment::class, 'draft_id')
            ->whereHas('draft', fn ($q) => $q->where('citizen_id', $this->id));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }
}