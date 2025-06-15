@props([
    'title',
    'value',
    'icon',
    'trend' => null,
    'trendValue' => null,
    'subtitle' => null,
    'href' => null,
    'color' => 'blue',
    'description' => null
])

@php
    $colorClasses = [
        'blue' => 'bg-gradient-to-r from-blue-500 to-purple-600',
        'green' => 'bg-gradient-to-r from-green-500 to-emerald-600',
        'yellow' => 'bg-gradient-to-r from-yellow-500 to-orange-600',
        'red' => 'bg-gradient-to-r from-red-500 to-pink-600',
        'purple' => 'bg-gradient-to-r from-purple-500 to-indigo-600',
        'indigo' => 'bg-gradient-to-r from-indigo-500 to-blue-600',
    ];
    
    $trendClasses = [
        'up' => 'text-green-600',
        'down' => 'text-red-600',
        'neutral' => 'text-gray-600'
    ];
@endphp

<div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
    @if($href)
        <a href="{{ $href }}" class="block text-decoration-none">
    @endif
    
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $colorClasses[$color] ?? $colorClasses['blue'] }}">
                {!! $icon !!}
            </div>
        </div>
        <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            
            @if($trend && $trendValue)
                <div class="flex items-center mt-1">
                    @if($trend === 'up')
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 010 0L21.75 9M21.75 9L18 5.25M21.75 9v4.5" />
                        </svg>
                    @elseif($trend === 'down')
                        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.511l-5.511-3.182" />
                        </svg>
                    @endif
                    <span class="text-sm {{ $trendClasses[$trend] ?? $trendClasses['neutral'] }} ml-1">{{ $trendValue }}</span>
                </div>
            @endif
            
            @if($subtitle)
                <p class="text-xs text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
            
            @if($description)
                <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>
    </div>
    
    @if($href)
        </a>
    @endif
</div> 