<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Autenticação em Dois Fatores</title>
  <style>
    .container {
      max-width: 500px;
      margin: auto;
      padding: 1%;
      font-family: sans-serif;
      color: black;
    }

    .highlight {
      color: #FF5900;
      font-weight: bold;
    }

    .mgtop10 {
      margin-top: 10px;
    }

    .centered {
      text-align: center;
    }
  </style>
</head>
<body>
<div style="text-align: center; ">
    <img alt="{{ config('app.name') }}" src="https://fusion-platafom.vercel.app/assets/logolaranja.png" width="180px">
  </div>
  <div class="container">
    <p>Olá, {{$name}}</p>
    <p>Para completar a sua <span class="highlight">autenticação em dois fatores</span>, por favor utilize o seguinte código:</p>
    <p class="mgtop10 highlight centered" style="font-size: 24px;">{{$token}}</p>
    <p>Se você não solicitou este código, por favor, ignore este e-mail.</p>
    <p>Atenciosamente,</p>
    <p>Equipe <span class="highlight">Fusion</span></p>
  </div>
</body>
</html>
