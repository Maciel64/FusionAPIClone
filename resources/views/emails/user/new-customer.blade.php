<x-mail::message>
# Olá {{ $user->name }} seja bem vindo (a)!

Sua conta foi criada com sucesso. Aguarde até que um de nossos moderadores valide suas informações. <br/>

Uma vez que sua conta tenha sido verificada, enviaremos um e-mail com seus dados de login. <br/>

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
