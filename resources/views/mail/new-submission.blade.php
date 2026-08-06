<x-mail::message>
# {{ $type }} baru dari {{ $name }}

{{ $details }}

<strong>Masuk ke panel admin</strong> untuk menindaklanjuti:
{{ route('filament.admin.pages.dashboard') }}

<x-mail::button :url="route('filament.admin.pages.dashboard')">
    Buka Admin Panel
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
