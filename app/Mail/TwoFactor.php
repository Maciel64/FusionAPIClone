<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TwoFactor extends Mailable
{
    use Queueable, SerializesModels;

    protected function __construct(string $subject, string $view, array $viewData = [])
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->viewData = $viewData;
    }
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.twoFactor',
        );
    }

    public static function sendMail(string $to, string $subject, string $view, array $viewData = [])
    {
        return Mail::to($to)->send(new static($subject, $view, $viewData));
    }

    public function build(): self
    {
      return $this;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
