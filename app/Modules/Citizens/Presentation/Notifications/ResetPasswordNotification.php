<?php

declare(strict_types=1);

namespace App\Modules\Citizens\Presentation\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {

        $frontendUrl = config('app.frontend_url', 'http://localhost:5174');

        $url = $frontendUrl . '/recuperar-password/' . $this->token . '?email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Restablecer contraseña — Portal PCSJ')
            ->greeting('Hola ' . $notifiable->full_name)
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expira en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este mensaje.');
    }
}