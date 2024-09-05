<x-mail::message>
# Olá {{ $user->name }}!

Validamos sua conta, agora você pode acessa-la clicando <a href="{{ config('settings.url_front') }}/login"> aqui </a><br/><br/>

Atensiosamente, <br/>

{{ config('app.name') }}
</x-mail::message>
