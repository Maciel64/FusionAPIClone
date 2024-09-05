<x-mail::message>

# Olá {{ $data['name'] }} seja bem vindo (a)!

<b>login:</b> {{ $data['email'] }}<br/>
<b>password:</b> {{ $data['password'] }}<br/><br/>

Para acessar, você deve primeiro verificar sua conta usando o código correspondente: <b>{{ $data['code'] }}</b><br/>

<a href="{{$data['url']}}">Clique aqui para verificar sua conta</a><br/><br/>

Observação: O código de validação expirará em {{ $data['expires_at'] }}<br/></br>


Atentiosamente,<br>
{{ config('app.name') }}
</x-mail::message>
