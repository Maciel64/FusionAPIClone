<?php

namespace App\Services;

use Exception;

class SendGridService
{

    public function send(string $to, string $subject, string $view, $data)
    {
      if(!config('services.sendgrid.enabled')) return true;
      
      $body = view($view, compact('data'))->render();
      $email = new \SendGrid\Mail\Mail();
      $email->setFrom(config('mail.from.address'), "Fusion");
      $email->setSubject($subject);
      $email->addTo($to, "Fusion App");
      $email->addContent("text/html", $body);

      $sendgrid = new \SendGrid(config('services.sendgrid.api_key'));
      try {
          $response = $sendgrid->send($email);
          if($response->statusCode() != 202)
            return false;
          return $response->body();
      } catch (Exception $e) {
          echo 'Caught exception: '. $e->getMessage() ."\n";
      }
    }
}
