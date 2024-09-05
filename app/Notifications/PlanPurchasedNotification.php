<?php

namespace App\Notifications;

use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanPurchasedNotification extends Notification
{
    use Queueable;

    protected Plan $plan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Plan $plan)
    {
      $this->plan = $plan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
      return (new MailMessage)
      ->subject('Contratação de Plano')
      ->greeting('Olá '. $notifiable->name)
      ->line('Você contratou o plano "' . $this->plan->name . '" com sucesso!')
      ->line('Obrigado por utilizar nosso serviço.')
      ->line('')
      ->line('Atenciosamente,')
      ->salutation('Equipe Fusion.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
