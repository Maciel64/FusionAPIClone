<?php

namespace App\Notifications;

use App\Models\Plan;
use App\Traits\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanUpdateScheduled extends Notification
{
    use Queueable, Helpers;

    protected Plan $currentPlan;
    protected Plan $requestPlan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($currentPlan, $requestPlan)
    {
      $this->currentPlan = $currentPlan;
      $this->requestPlan = $requestPlan;  
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
        ->subject('Solicitação de atualização de plano confirmada')
        ->greeting('Olá '. $notifiable->name)
        ->line('Recebemos sua solicitação para atualizar seu plano atual "'.$this->currentPlan->name.'" no valor de '. $this->formatMoney($this->currentPlan->price/100, "BRL") 
        .' " para o plano "'.$this->requestPlan->name.'" no valor de '. $this->formatMoney($this->requestPlan->price/100, "BRL") .'"')
        ->line('Gostaríamos de informar que a atualização do plano será efetuada após o término da vigência do plano atual e a confirmação do pagamento.')
        ->line('Assim que seu plano for atualizado, a cobrança será ajustada de acordo com o valor do plano solicitado.')
        ->line('Enviaremos uma notificação para confirmar a atualização do plano e informar os detalhes da nova cobrança.')
        ->line('Caso você tenha alguma dúvida ou precise de mais informações, não hesite em entrar em contato conosco.')
        ->line('Agradecemos por escolher nossos serviços e esperamos que você continue aproveitando todos os recursos e benefícios oferecidos.')
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
