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
      width: 93.5;
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
      <h1 style="margin-top:25px; color:#FF5900">{{$firstName}},</h1>
      <h1>Seu espaço está preparado e esperando por você.</h1>
    </div>
    <div style=" margin: 20px auto; display:{{$display}};">
      <img style="border-radius: 15px; margin: auto; object-fit: cover;" src={{$photo}} alt="Image room" width="100%" height="242.32" />
    </div>

    <div>

      <section>
        <h2 style="margin: 10px 0px 0px 0px; color:#FF5900;" >{{$room->coworking->name}}</h2>

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
        <!-- <hr> -->

        <div>
          <h2 style="margin-top:15px;"><strong>Endereço</strong></h2>
          <p style="margin:5px 0px 0px 0px;">{{$room->address->line_1}}, {{$room->address->line_2}} - {{$room->coworking->name}}</p>
          <p style="margin:0px;">{{$room->address->city}}, {{$room->address->neighborhood}} - {{$room->address->state}},
            {{$room->address->zip_code}}
          </p>
        </div>

        <a class="button" style="margin-top:20px;" 
        href="https://www.google.com/maps/search/?api=1&query={{$addressToGoogle}}">Como Chegar</a>
        <hr style="margin-top:20px;">


        <table style="width:100%; border-collapse: collapse; margin-top:1px;">
          <tr>
            <th style="text-align: start; font-weight: bold; width:50%; ">
              <h2 style="color:#FF5900;">Check-in</h2>
            </th>
            <th style="text-align: end; font-weight: bold; width:50%; ">
              <h2 style="color:#FF5900;">Checkout</h2>
            </th>
          </tr>

          @foreach($emailAppointments as $emailAppointment)
          <tr style="margin-top:5px">
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
        <br>
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
        <hr>
    </div>
    <div>

      @if($role !== 'admin' && $role !== 'owner')
      <h2>Pagamentos</h2>
      <table class="mgtop10" style="width:100%; border-collapse: collapse;">
        <tr>
          <th style="text-align: start; width:50%;">
            {{ ucfirst($user->card->brand) }} **** **** **** {{ $user->card->last_four_digits }}
            <br>{{ $emailAppointments[0]->appointment->created_at }}
          </th>
          <th style="text-align: end; width:50%;">
            {{ $valueTotalAppointments }} à vista
          </th>
        </tr>
      </table> 
      <hr class="mgtop10">
      @endif

      <h2>A Fusion Clinic está aqui para <span style="color:#FF5900">apoiar em cada passo</span> da sua jornada.</h2>

      <table class="mgtop10" style="width:100%; border-collapse: collapse;">
        <tr>
          <th style="text-align: start; width:50%; ">
            <h3>Saiba o que esperar <br> <span>Lembre-se de revisar as regras do espaço.</span></h3>
          </th>
          <th style="text-align: end;  width:50%; cursor: pointer;"><a href="https://www.fusionclinic.com.br/politicas-de-cancelamento" style="color:#FF5900">Acessar termos de uso</a></th>
        </tr>
      </table>

      <table class="mgtop10" style="width:100%; border-collapse: collapse;">
        <tr>
          <th style="text-align: start; width:50%; ">
            <h3>Atendimento ao cliente <br> <span>Entre em contato com nossa equipe de atendimento</span></h3>
          </th>
          <th style="text-align: end;  width:50%; "><a href="https://wa.me/5511919119054?text=Ol%C3%A1%21+Fiz+uma+reserva+e+preciso+falar+com+a+Fusion; cursor: pointer;" style="color:#FF5900">Fale com a Fusion</a></th>
        </tr>
      </table>

    </div>
  </section>

</div>
</section>
</div>

</html>