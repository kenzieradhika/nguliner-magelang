@props(['name' => '', 'class' => 'h-4 w-4'])

<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <use href="#ng-{{ $name }}" />
</svg>
