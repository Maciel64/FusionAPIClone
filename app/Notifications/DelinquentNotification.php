<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DelinquentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
      
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
        ->subject('Aviso de inadimplência')
        ->greeting('Olá!')
        ->line('Prezado(a) '. $notifiable->name .',')
        ->line('Por meio desta comunicação, gostaríamos de informá-lo sobre as possíveis consequências da inadimplência com a plataforma Fusion.')
        ->line('É importante ressaltar que, ao utilizar nossos serviços, você concorda com os termos e condições de uso, que incluem o pagamento das taxas e valores referentes ao uso da plataforma.')
        ->line('Caso ocorra a falta de pagamento, a Fusion poderá adotar medidas legais cabíveis para proteger seus direitos, tais como:')
        ->line('- 1. Interrupção do serviço: a Fusion poderá suspender o seu acesso à plataforma até que a dívida seja regularizada.')
        ->line('- 2. Cobrança de juros e multas: caso haja atraso no pagamento, poderão ser aplicados juros e multas sobre o valor devido, conforme estipulado em contrato.')
        ->line('- 3. Inscrição em órgãos de proteção ao crédito: a Fusion poderá incluir seu nome em cadastros de inadimplentes, como o Serasa e o SPC, o que poderá dificultar o acesso a crédito em outras empresas.')
        ->line('- 4. Ação judicial: em última instância, a Fusion poderá ingressar com ação judicial para cobrar os valores devidos, o que pode gerar custos adicionais para o usuário, como honorários advocatícios e custas processuais.')
        ->line('Por isso, recomendamos que, em caso de dificuldades financeiras, entre em contato conosco para buscar alternativas de pagamento e evitar a inadimplência.')
        ->line('Esperamos contar com a sua compreensão e colaboração para mantermos uma relação transparente e saudável entre a Fusion e seus usuários.')
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
