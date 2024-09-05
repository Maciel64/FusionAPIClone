<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendgridTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $address = 'lf.system@outlook.com';
      $subject = 'Teste de envio de email com SendGrid';
      $name = 'Teste de envio de email com SendGrid';

      return $this->view('emails.test')
                  ->subject($subject)
                  ->from($address, $name)
                  ->to('lf.system@outlook.com') // email to send
                  ->with([ 'test_message' => $this->data ]);
    }
}
