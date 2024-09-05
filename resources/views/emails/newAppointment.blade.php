
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
      <style>
        * {
        margin: 0;
        padding: 0;
        }
        html, body {
          height: 100%;
          width: 100%;
        }
        .section {
          width: 100%;
          max-width: 800.42px;
          margin: 0 auto;
        }
        .flex {
          display: flex;
        }
        .wrap {
          flex-wrap: wrap;
        }
        .image {
          width: 100%;
          min-width: 280px;
          max-width: 800.42px;
          height: 242.32px;
          margin: 2px 0px;
        }
        .div-info {
          margin-top: 15px;
          width: 100%;
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
          align-items: center;
        }
        @media (max-width: 580px) {
          .div-info {
            flex-direction: column;
            align-items: start;
          }
          .flex-column{
            flex-direction: 'column';
            justify-content: 'left'; 
          }
          .th {
            padding: '0 1.5px';
          }
          .td {
            margin: 2px;
            padding-bottom: 2px;
          }
          .image {
            background-position: center;
            background-size: cover;
          }
        }
      </style>
  </head>
  <body>
    <div style="text-align: center; margin-bottom: 15px;">
      <img alt="{{ config('app.name') }}" 
      src="{{ config('app.frontend_url') }}/assets/Fusion-Email-Logo.png"
           width="180px">
    </div>
    <section style="background-color: #FFFFFF; padding: 1%; border-radius: 20px; font-family: sans-serif;" class="section">
      <div style="display: flex; justify-content: space-between; width: 100%; max-width: 800.42px; margin: auto; flex-direction: column;">
        <div>
          <h1 style="font-weight: bold; color: #464E5F; font-size: 2xl;">RESERVA DE SALA</h1>
          <!-- <h3 style="font-weight: semibold; color: #464E5F;">#{{$room->coworking->name}}</h3> -->
        </div>
        <div style="color: #B5B5C3; display: none;">
          <p>`${room?.address?.line_1}, ${room?.address?.line_2}, ${room?.address?.neighborhood}`</p>
          <p>`${room?.address?.city}/${room?.address?.state}, CEP ${room?.address?.zip_code}`</p>
        </div>
      </div>
      <div style="margin: auto; border-radius: 28px; display:{{$display}};" class="image">
        <img style="background-size: cover; background-position: 50% 50%; border-radius: 28px; margin: auto; margin-top: 10px; object-fit: cover; object-position: 50% 50%;" 
        src="{{$photo}}" alt="image room" width="100%" height="242.32" />
      </div>
      <snewAppointment.bladeection style="flex-direction: column-reverse; " class="section flex">
        <div style="width: 100%;">
        <div class="div-info">
            <div style="text-align: center;">
              <p style="padding-bottom: 5px; color: #B5B5C3; font-weight: semibold; text-align: start;" class="th"><bold>SALA</bold></p>
              <p style="padding-bottom: 5px; padding-top: 5px; font-weight: semibold; color: #464E5F; text-align: start" class="th">{{$room->name}}, nº {{$room->number}}</p>
            </div>
            <div style="padding-bottom: 5px; padding-top: 5px; text-align: start; font-weight: semibold; color: #464E5F;">
              <p style="padding-bottom: 5px; color: #B5B5C3; font-weight: semibold; text-align: start" class="th"><bold>DATA DE ENTRADA</bold></p>
              <p style="margin: 2px; font-weight: bold; color: #464E5F;">{{ $formattedTime }} às {{$formattedDateInit}}</p>
            </div>
            <div style="padding-bottom: 5px; padding-top: 5px; text-align: start; font-weight: semibold; color: #464E5F;">
              <p style="padding-bottom: 5px; color: #B5B5C3; font-weight: semibold; text-align: start" class="th"><bold>DATA DE SAÍDA</bold></p>
              <p style="margin: 2px; font-weight: bold; color: #464E5F;">{{ $formattedTime }} às {{$formattedDateEnd}}</p>
            </div>
          </div>
          <section class="section">
            <h2 style="font-weight: semibold; margin: 10px 0;">DETALHES DO AGENDAMENTO</h2>
            <div style="width: 100%;">
              <div style="width: 100%;">
                <p style="font-weight: font: 600; color: #464E5F"><strong>Clínica</strong></p> 
                <p style="padding: 4px; color: #464E5F;">{{$room->coworking->name}}</p>
              </div>
              <div style="width: 100%;">
                <p style="font-weight: font: 600; color: #464E5F"><strong>Endereço</strong></p>
                <p style="padding: 4px; color: #464E5F; width: 100%">{{$room->address->line_1}}, {{$room->address->line_2}}</p>
                <p style="padding: 4px; color: #464E5F; width: 100%;">{{$room->address->neighborhood}}</p>
                <p style="padding: 4px; color: #464E5F; width: 100%;">{{$room->address->city}} - {{$room->address->state}}</p>
              </div>
            </div>
          </section>
        </div>        
        <div style="width: 100%; text-align: right; padding: 2px; margin-top: 10px;">
          <div style="text-align: start; padding: 5px;">
            <h3 style="font-weight: 600; color: #B5B5C3;">VALOR TOTAL</h3>
            <h2 style="font-size: x-large; font-weight: bold; color: #464E5F;">R$ {{$appointment->value_total}}</h2>
            <p style="color: #B5B5C3; font-size: small;">Taxas inclusas</p>
          </div>
        </div>
      </snewAppointment.bladeection>
    </section>
  </body>
</html>