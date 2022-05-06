<nav id="js-nav-menu" class="w-auto px-2 pt-6 pb-2 bg-gray-200 shadow hidden lg:hidden">
    <ul class="my-0">
        @foreach($page->navItems as $navItem)
            <li class="pl-4">
                <a title="{{ $page->siteName }} {{ $navItem->title }}"
                   href="{{ $navItem->url }}"
                   class="block mt-0 mb-4 text-sm no-underline {{ $page->isActive($navItem->url) ? 'active text-blue-500' : 'text-gray-800 hover:text-blue-500' }}"
                >
                    {{ $navItem->title }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
