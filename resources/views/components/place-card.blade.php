@props(['place'])

<a href="{{ route('place.show', $place->slug) }}" class="group block">
    <div class="relative overflow-hidden rounded-2xl bg-neutral-100">
        <img src="{{ $place->image }}" alt="{{ $place->name }}" loading="lazy" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            @if($place->is_legendary)
                <span class="ng-tag">Legendaris</span>
            @endif
            @if($place->isOpenNow())
                <span class="inline-flex items-center gap-1 rounded-full bg-green-600 px-3 py-1 text-xs font-medium text-white">
                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span> Buka
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-neutral-900/80 px-3 py-1 text-xs font-medium text-white">Tutup</span>
            @endif
        </div>
    </div>
    <div class="mt-4">
        <h3 class="text-base font-semibold leading-snug transition group-hover:underline">{{ $place->name }}</h3>
        <p class="mt-1 line-clamp-1 text-sm text-neutral-500">{{ $place->tagline }}</p>
        <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-neutral-400">
            <span>{{ $place->category?->name }}</span>
            @if($place->averageRating() > 0)
                <span class="flex items-center gap-1 text-neutral-600">
                    <x-rating-stars :rating="$place->averageRating()" />
                    <span>{{ number_format($place->averageRating(), 1) }}</span>
                </span>
            @endif
            @if($place->price_range)
                <span>{{ $place->price_range }}</span>
            @endif
        </div>
    </div>
</a>
