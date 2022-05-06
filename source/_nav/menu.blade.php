<nav class="hidden lg:flex items-center justify-end text-lg">
    @foreach($page->navItems as $navItem)
        <a title="{{ $page->siteName }} {{ $navItem->title }}" href="{{ $navItem->url }}"
           class="ml-6 text-gray-700 hover:text-blue-600 {{ $page->isActive($navItem->url) ? 'active text-blue-600' : '' }}">
            {{ $navItem->title }}
        </a>
    @endforeach
</nav>
