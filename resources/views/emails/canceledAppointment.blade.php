@extends('emails.layout-canceled')

@section('content')
  <div style=" justify-content: center; margin: auto">
    <br>
    <p style="color: #14161a;font-size: 16px;">     
    <p><strong>Cancelamento de Agendamento - Devolução de Valor via Chave Pix</strong></p>
    <p style="color: #14161a;font-size: 16px;"> </p>
    <p>Prezado(a) <strong>{{ $appointment->customer->name }}</strong>,</p>

    <p>Gostaríamos de informar que o seu agendamento para a sala <strong>{{ $appointment->room->name }}</strong> na clínica <strong>{{ $appointment->room->coworking_name }}</strong>, marcado para o dia <strong>{{$formattedDateTime}}</strong>, foi cancelado.</p>

    <p>Para garantir o reembolso do valor referente ao agendamento cancelado, solicitamos que nos forneça uma chave Pix para que possamos processar o estorno de forma rápida e eficiente. Favor enviar para o seguinte endereço de e-mail: <strong>contato@fusionclinic.com.br</strong>. Assim que recebermos a chave iremos proceder com o estorno em até 30 dias corridos.</p>

    <p>Estamos à disposição para qualquer esclarecimento. Valorizamos a sua confiança em nossos serviços e esperamos poder atendê-lo(a) novamente em breve.</p>

    <p>Agradecemos a compreensão e colaboração.</p>

    </p>
    <p style="color: #14161a;font-size: 16px;">
      Atenciosamente,
    </p>
    <p>Equipe Fusion</p>

  </div>
@endsection
