<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu correo — Portal PCSJ')
            ->greeting('Bienvenido/a, ' . $notifiable->full_name)
            ->line('Gracias por registrarte en el Portal de Solicitudes de Jubilación.')
            ->action('Verificar correo', $verificationUrl)
            ->line('Si no creaste esta cuenta, ignora este mensaje.');
    }

    protected function verificationUrl($notifiable): string
    {
      
        return URL::temporarySignedRoute(
            'citizens.verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}