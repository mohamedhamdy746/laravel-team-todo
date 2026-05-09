@props([
    'type' => 'primary',
    'href' => null,
    'submit' => false,
])

@php
    $base = 'inline-flex items-center justify-center px-5 py-2 rounded-lg '
        .'text-sm font-semibold leading-normal transition-all duration-200 '
        .'focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50';

    $variant = match ($type) {
        'secondary' => 'bg-white text-slate-700 border-gray-200 '
            .'hover:bg-gray-50 hover:border-gray-300 '
            .'focus:ring-gray-200 shadow-sm',
        'danger' => 'bg-rose-600 text-white border-transparent '
            .'hover:bg-rose-700 focus:ring-rose-200 shadow-sm',
        default => 'bg-blue-600 text-white border-transparent '
            .'hover:bg-blue-700 focus:ring-blue-200 shadow-sm',
    };

    $classes = $base.' '.$variant;
    $buttonType = $submit ? 'submit' : 'button';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $buttonType }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
