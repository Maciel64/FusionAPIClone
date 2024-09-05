<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GenericMail extends Mailable 
{
  use Queueable, SerializesModels;

  protected function __construct(string $subject, string $view, array $viewData = [])
  {
    $this->subject = $subject;
    $this->view = $view;
    $this->viewData = $viewData;
  }

  public function build(): self
  {
    return $this;
  }

  public static function sendMail(string $to, string $subject, string $view, array $viewData = [])
  {
    // return Mail::to($to)->queue(new static($subject, $view, $viewData));
    return Mail::to($to)->send(new static($subject, $view, $viewData));
  }
}
