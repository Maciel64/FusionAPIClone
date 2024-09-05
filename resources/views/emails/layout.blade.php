<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>be.pass</title>
  <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
  <style>@import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);</style>
  <style>
    body {
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      background-color:#F4F4F4;
    }
    img {
      border: 0;
      line-height: 100%;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }
    .content {
      margin: 45px 0;
    }
    .btn-send {
      background: #193980;
      color: white !important;
      padding: 10px 20px;
      border: none;
      cursor: pointer;
      border-radius: 30px;
      text-decoration: none;
      transition: background 0.03s ease;
    }
    .btn-send:hover {
      background: #2357be;
    }
  </style>
</head>
<body>
  <div style="margin-top:15px auto 0 auto; max-width:600px;">
    <div class="header" style="text-align: center;">
      <img alt="{{ config('app.name') }}" 
           src="{{ config('app.frontend_url') }}/assets/Fusion-Email-Logo.png"
           width="180px">
    </div>
    <div class="content">
      @yield('content')
    </div>
    <div class="footer" style="text-align: center;">
      <img alt="{{ config('app.name') }}" 
      src="{{ config('app.frontend_url') }}/assets/Fusion-Email-Logo.png"
           width="180px">
    </div>
  </div>
</body>
</html>