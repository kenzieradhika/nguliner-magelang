@extends('layouts.app')

@section('meta_title', 'Peta Kuliner Magelang — Cari Lokasi Tempat Makan Terdekat')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map { height: calc(100vh - 10rem); z-index: 0; }
        .leaflet-popup-content-wrapper { border-radius: 12px; }
        .leaflet-popup-content { margin: 14px 18px; }
    </style>
@endsection

@section('content')
    <section class="border-b border-neutral-200 bg-neutral-50 py-12">
        <div class="ng-container">
            <h1 class="text-3xl font-bold tracking-tight">Peta Kuliner Magelang</h1>
            <p class="mt-3 max-w-2xl text-sm text-neutral-500">Semua lokasi kuliner rekomendasi NGuliner dalam satu peta. Klik marker untuk detail.</p>
        </div>
    </section>

    <section class="py-8">
        <div class="ng-container">
            <div id="map" class="rounded-2xl border border-neutral-200"></div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const places = @json($places);
        const map = L.map('map').setView([-7.4750, 110.2150], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        places.forEach(place => {
            const marker = L.marker([place.latitude, place.longitude]).addTo(map);
            const html = `
                <div class="w-52">
                    <img src="${place.image}" alt="${place.name}" class="h-24 w-full rounded-lg object-cover" loading="lazy">
                    <p class="mt-2 text-sm font-bold">${place.name}</p>
                    <p class="text-xs text-neutral-500 line-clamp-1">${place.tagline ?? ''}</p>
                    <a href="${'/kuliner/' + place.slug}" class="mt-2 inline-block text-xs font-semibold underline">Lihat detail</a>
                </div>`;
            marker.bindPopup(html, { minWidth: 200 });
        });

        const bounds = L.latLngBounds(places.map(p => [p.latitude, p.longitude]));
        if (places.length > 1) map.fitBounds(bounds, { padding: [40, 40] });
    </script>
@endpush
