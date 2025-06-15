@props([
    'title',
    'description' => null,
    'action' => null,
    'columns' => 4,
    'class' => 'mb-8'
])

<div class="{{ $class }}">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-500 tracking-wide uppercase">{{ $title }}</h3>
            @if($description)
                <p class="text-sm text-gray-400 mt-1">{{ $description }}</p>
            @endif
        </div>
        @if($action)
            <div>
                {{ $action }}
            </div>
        @endif
    </div>
    
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-{{ $columns }}">
        {{ $slot }}
    </div>
</div> 