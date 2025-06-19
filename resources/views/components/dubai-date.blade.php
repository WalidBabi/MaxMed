@props(['date', 'format' => 'M d, Y H:i', 'showTimezone' => true, 'showTime' => true])

@if($date)
    <span {{ $attributes->merge(['class' => '']) }}>
        @if($showTime)
            {{ formatDubaiDate($date, $format) }}
            @if($showTimezone)
                <span class="text-xs text-gray-500 ml-1">(Dubai)</span>
            @endif
        @else
            {{ formatDubaiDate($date, 'M d, Y') }}
        @endif
    </span>
@else
    <span {{ $attributes->merge(['class' => 'text-gray-400']) }}>N/A</span>
@endif 