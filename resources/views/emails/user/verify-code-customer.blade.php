<x-mail::message>

# Olá {{ $data['name'] }} seja bem vindo (a)!

Para acessar, você deve primeiro verificar sua conta usando o código correspondente: <b>{{ $data['code'] }}</b><br/>

<a href="{{$data['url']}}">Clique aqui para verificar sua conta</a><br/><br/>

Observação: O código de validação expirará em {{ $data['expires_at'] }}<br/></br>


Atentiosamente,<br>
{{ config('app.name') }}
</x-mail::message>
