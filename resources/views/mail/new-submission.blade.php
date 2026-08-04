<x-mail::message>
# {{ $type }} baru dari {{ $name }}

{{ $details }}

<strong>Masuk ke panel admin</strong> untuk menindaklanjuti:
{{ route('admin.dashboard') }}

<x-mail::button :url="route('admin.dashboard')">
    Buka Admin Panel
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
