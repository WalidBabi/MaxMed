@props([
    'activities' => [],
    'title' => 'Recent Activities',
    'viewAllUrl' => null,
    'emptyMessage' => 'No activities yet',
    'emptyDescription' => 'Activities will appear here once they are created.',
    'limit' => 5
])

<div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @if($viewAllUrl)
                <a href="{{ $viewAllUrl }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all</a>
            @endif
        </div>
    </div>
    <div class="p-6">
        @if(count($activities) > 0)
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($activities->take($limit) as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        @if(isset($activity['icon']))
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white {{ $activity['iconBg'] ?? 'bg-gray-500' }}">
                                                {!! $activity['icon'] !!}
                                            </span>
                                        @else
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white bg-gray-500">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] ?? $activity['subject'] ?? 'Activity' }}</p>
                                            <p class="text-sm text-gray-500">{{ $activity['description'] ?? $activity['lead']['full_name'] ?? 'No description' }}</p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                            <time datetime="{{ $activity['created_at'] ?? now() }}">
                                                {{ isset($activity['created_at']) ? $activity['created_at']->diffForHumans() : 'Just now' }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <x-empty-state 
                :title="$emptyMessage"
                :message="$emptyDescription"
                :icon="'<svg class=\"h-12 w-12 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\">
                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h4.125m0-15.75c0-1.036.84-1.875 1.875-1.875h5.25c1.035 0 1.875.84 1.875 1.875v15.75c0 .621-.504 1.125-1.125 1.125H9.75M8.25 9.75h4.5v2.25H8.25V9.75z\" />
                </svg>'"
            />
        @endif
    </div>
</div> 