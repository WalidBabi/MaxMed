@props(['items'])

<nav aria-label="breadcrumb" class="py-2 px-4 bg-gray-50">
    <ol class="flex items-center space-x-2 text-sm" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700" itemprop="item">
                <span itemprop="name">Home</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        
        @foreach($items as $key => $item)
            <li class="text-gray-500">/</li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if(!$loop->last)
                    <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-700" itemprop="item">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                @else
                    <span class="text-gray-700" itemprop="name">{{ $item['name'] }}</span>
                @endif
                <meta itemprop="position" content="{{ $key + 2 }}" />
            </li>
        @endforeach
    </ol>
</nav> 