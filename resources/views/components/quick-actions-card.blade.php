@props([
    'title' => 'Quick Actions',
    'actions' => [],
    'class' => ''
])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 {{ $class }}">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    <div class="p-6 space-y-4">
        @if(count($actions) > 0)
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2 border {{ $action['style'] ?? 'border-gray-300 text-gray-700 bg-white' }} text-sm font-medium rounded-md hover:{{ $action['hover'] ?? 'bg-gray-50' }}">
                    @if(isset($action['icon']))
                        {!! $action['icon'] !!}
                    @endif
                    {{ $action['label'] }}
                </a>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>
</div> 