<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = env('APP_FRONT_URL').'password-reset?token='.$this->token.'&email='.$notifiable->getEmailForPasswordReset();

        return (new MailMessage)
            ->subject(__('Redefina sua senha!'))
            ->line(__('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.'))
            ->action(__('Redefinir senha'), $url)
            ->line(__('Este link de redefinição de senha expirará em :count minutos.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(__('Se você não solicitou uma redefinição de senha, nenhuma outra ação é necessária.'));

            
    }
}