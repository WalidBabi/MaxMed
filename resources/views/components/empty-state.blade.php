@props([
    'title' => 'No data available',
    'message' => null,
    'icon' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'class' => 'text-center py-8'
])

<div class="{{ $class }}">
    @if($icon)
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
            {!! $icon !!}
        </div>
    @else
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125m0-15.75c0-1.036.84-1.875 1.875-1.875h5.25c1.035 0 1.875.84 1.875 1.875v15.75c0 .621-.504 1.125-1.125 1.125H9.75M8.25 9.75h4.5v2.25H8.25V9.75z" />
        </svg>
    @endif
    
    <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ $title }}</h3>
    
    @if($message)
        <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
    @endif
    
    @if($actionLabel && $actionUrl)
        <div class="mt-6">
            <a href="{{ $actionUrl }}" 
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                {{ $actionLabel }}
            </a>
        </div>
    @endif
    
    {{ $slot }}
</div> 