@props([
    'title',
    'stats' => [],
    'description' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'icon' => null,
    'color' => 'blue'
])

@php
    $colorClasses = [
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'purple' => 'text-purple-600',
        'orange' => 'text-orange-600',
        'red' => 'text-red-600',
    ];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            @if($icon)
                <span class="w-5 h-5 {{ $colorClasses[$color] ?? $colorClasses['blue'] }} mr-2">
                    {!! $icon !!}
                </span>
            @endif
            {{ $title }}
        </h3>
    </div>
    <div class="p-6">
        @if(count($stats) > 0)
            <div class="grid grid-cols-1 md:grid-cols-{{ count($stats) > 2 ? 'auto' : count($stats) }} gap-4 mb-6">
                @foreach($stats as $stat)
                    <div class="bg-{{ $stat['color'] ?? 'gray' }}-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-{{ $stat['color'] ?? 'gray' }}-900">{{ $stat['value'] }}</div>
                        <div class="text-sm text-{{ $stat['color'] ?? 'gray' }}-600">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{ $slot }}
        
        @if($description || ($actionLabel && $actionUrl))
            <div class="flex justify-between items-center {{ count($stats) > 0 ? 'mt-4' : '' }}">
                @if($description)
                    <p class="text-gray-600">{{ $description }}</p>
                @endif
                @if($actionLabel && $actionUrl)
                    <a href="{{ $actionUrl }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-{{ $color }}-600 hover:bg-{{ $color }}-700">
                        {{ $actionLabel }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div> 