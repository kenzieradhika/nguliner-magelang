@props(['rating'])

<span class="inline-flex text-amber-400">
    @for($i = 1; $i <= 5; $i++)
        <svg class="h-3.5 w-3.5 {{ $i <= round($rating) ? '' : 'text-neutral-300' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9l-5.2 2.7 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
    @endfor
</span>
