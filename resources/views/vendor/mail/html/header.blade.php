@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Fusion App')
<img src="{{ env('APP_FRONT_URL') }}/assets/Fusion-Email-Logo.png" alt="Logo da {{ env('APP_NAME') }}">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
