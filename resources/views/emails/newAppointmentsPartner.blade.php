<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    * {
      color: black;
    }

    .mgtop10 {
      margin-top: 10px;
    }

    .container {
      max-width: 500px;
      margin: auto;
      padding: 1%;
      font-family: sans-serif;
      color: black;
    }

    .button {
      width: 94%;
      border: 2px solid #000;
      border-color: #FF5900;
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      font-size: 16px;
      background-color: transparent;
      display: block;
      text-decoration: none;
      color: #FF5900;
      margin: auto;
    }

    .button:hover {
      background-color: #fffcfa;
    }
  </style>
</head>
<div class="container">

  <div style="text-align: center; ">
    <img alt="{{ config('app.name') }}" src="{{ env('FRONTEND_URL') }}/assets/logolaranja.png" width="180px">
  </div>

  <section>
    <div>
      <h1 style="margin-top:25px; color:#FF5900;">{{$partnerFirstName}},</h1>
      <h1>Excelente notícia! Temos uma nova reserva confirmada no seu espaço.</h1>
    </div>
    <div style=" margin: 20px auto; display:{{$display}};">
      <img style="border-radius: 15px; margin: auto; object-fit: cover;" src={{$photo}} alt="Image room" width="100%" height="242.32" />
    </div>

    <div>

      <section>
        <h2 style="margin: 10px 0px 0px 0px">{{$room->coworking->name}}</h2>

        <img style="width:3.5%; margin: 0px;" src="{{ env('FRONTEND_URL') }}/assets/localizacaolaranja.png" alt="">
        <span style="font-size: 0.735rem; vertical-align: top;">{{$room->address->city}}, {{$room->address->neighborhood}}</span>



        <div class="mgtop10">
          @foreach($room->facilities as $facilities)
          <div style="margin-top: 8px;">
            <img style="width:3.5%; margin:0px; color:#FF5900;" src="{{ env('FRONTEND_URL') }}/assets/verificacaolaranja.png" alt="">
            <span style="margin:0px; color:#FF5900;vertical-align: top;">{{$facilities['name']}}</p>
          </div>
          @endforeach
        </div>
        <div class="margin-top:27px;"></div>

        <hr style="margin-top:17px;">

        <h2 style="margin-top:15px;"><strong>Dados da Reserva</strong></h2>

        <table style=" margin-top:5px; width:100%; border-collapse: collapse;">
          <tr>
            <td style="text-align: start; width:50%; ">Profissional:</td>
            <td style="text-align: end; width:50%; ">{{$user->name}} horas</td>
          </tr>
        </table>
        <table style=" margin-top:3px; width:100%; border-collapse: collapse;">
          <tr>
            <td style="text-align: start; width:50%; ">Especialidade:</td>
            <td style="text-align: end; width:50%; ">{{$user->healthAdvice->health_advice}} </td>
          </tr>
        </table>

        <table style=" margin-top:3px; width:100%; border-collapse: collapse;">
          <tr>
            <th style="text-align: start; font-weight: bold; width:50%; ">
              <h2 style="color:#FF5900;">Check-in</h2>
            </th>
            <th style="text-align: end; font-weight: bold; width:50%; ">
              <h2 style="color:#FF5900;">Checkout</h2>
            </th>
          </tr>

          @foreach($emailAppointments as $emailAppointment)

          <tr style="margin-top:2px">
            <td style="text-align: start; width:50%; "><strong style="font-weight: bold;">{{$emailAppointment->formatedDay}}</strong><br> às {{ $emailAppointment->formattedDateInit }}
              <a href="{{$emailAppointment->googleCalendar}}">
                <img style="width:5.5%; margin:0px;" src="https://i.ibb.co/WxCnvNJ/google-calendar-icon.png" alt=""> Google agenda</a>
            </td>
            <td style="text-align: end; width:50%; "><strong style="font-weight: bold;">{{$emailAppointment->formatedDay}}</strong><br> às {{ $emailAppointment->formattedDateEnd }}</td>
          </tr>
          <br>
          @endforeach
        </table>
        <table class="mgtop10" style="width:100%; border-collapse: collapse;">
          <tr>
            <td style="text-align: start; width:50%; ">Preço da hora reservada</th>
            <td style="text-align: end;  width:50%; ">{{$uniqueValue}}</th>
          </tr>
        </table>
        <table style=" margin-top:5px; width:100%; border-collapse: collapse;">
          <tr>
            <td style="text-align: start; width:50%; ">Quantidade de hora reservada</td>
            <td style="text-align: end; width:50%; ">{{count($emailAppointments)}} horas</td>
          </tr>
        </table>
        <table style="width:100%; border-collapse: collapse;">
          <tr>
            <th style="text-align: start; font-weight: bold; width:50%; ">
              <h2>Total</h2>
            </th>
            <th style="text-align: end; font-weight: bold; width:50%; ">
              <h2>{{$valueTotalAppointments}}</h2>
            </th>
          </tr>
        </table>
    </div>
    <div>


      <hr class="mgtop10">

      <h3>Não pode receber esse(a) profissional?</h3>

      <a class="button" style="margin-top:20px; href=" https://wa.me/5511919119054?text=Ol%C3%A1%21+Fiz+uma+reserva+e+preciso+falar+com+a+Fusion">Entrar em contato</a>

      <h3>Para garantir a melhor experiência</h3>

      <div style="margin-top: 8px;">
        <img style="width:3.5%; margin:0px;" src="{{ env('FRONTEND_URL') }}/assets/verificacaolaranja.png" alt="">
        <span style="margin:0px; color: #FF5900; vertical-align: top;">Na nossa plataforma, você encontra todos os detalhes da reserva.</span>
      </div>


      <div style="margin-top: 8px;">
        <img style="width:3.5%; margin:0px;" src="{{ env('FRONTEND_URL') }}/assets/verificacaolaranja.png" alt="">
        <span style="margin:0px; color: #FF5900; vertical-align: top;">O pagamento já foi processado antecipadamente no nosso sistema.</span>
      </div>

      <div style="margin-top: 8px;">
        <img style="width:3.5%; margin:0px;" src="{{ env('FRONTEND_URL') }}/assets/verificacaolaranja.png" alt="">
        <span style="margin:0px; color: #FF5900; vertical-align: top;">Sugerimos que o ambiente esteja limpo e organizado.</span>
      </div>


      <div style="margin-top: 8px;">
        <img style="width:3.5%; margin:0px;" src="{{ env('FRONTEND_URL') }}/assets/verificacaolaranja.png" alt="">
        <span style="margin:0px; color: #FF5900; vertical-align: top;">Se houver, recomendamos que a equipe de recepção esteja a par.</span>
      </div>


      <table class="mgtop10" style="width:100%; border-collapse: collapse;">
        <tr>
          <th style="text-align: start; width:50%; ">
            <h3>Saiba o que esperar <br> <span>Lembre-se de revisar as <br>Regras de Anfitrião.</span></h3>
          </th>
          <th style="text-align: end;  width:50%; cursor: pointer; color: #FF5900;"><a style="color: #FF5900;" href="https://www.fusionclinic.com.br/politicas-de-cancelamento">Acessar termos de uso</a></th>
        </tr>
      </table>

      <table class="mgtop10" style="width:100%; border-collapse: collapse;">
        <tr>
          <th style="text-align: start; width:50%; ">
            <h3>Atendimento ao cliente <br> <span>Entre em contato com nossa equipe de atendimento</span></h3>
          </th>
          <th style="text-align: end;  width:50%; cursor: pointer; color: #FF5900;"><a style="color: #FF5900;" href="https://wa.me/5511919119054?text=Ol%C3%A1%21+Fiz+uma+reserva+e+preciso+falar+com+a+Fusion">Fale com a Fusion</a></th>
        </tr>
      </table>

    </div>
  </section>

</div>
</section>
</div>

</html>