<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="{{ $page->description ?? $page->siteDescription }}">

    <meta property="og:title" content="{{ $page->title ? $page->title . ' | ' : '' }}{{ $page->siteName }}"/>
    <meta property="og:type" content="{{ $page->type ?? 'website' }}"/>
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="{{ $page->description ?? $page->siteDescription }}"/>

    <title>{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}</title>

    <link rel="home" href="{{ $page->baseUrl }}">
    <link rel="icon" href="/favicon.ico">
    <link href="/blog/feed.atom" type="application/atom+xml" rel="alternate" title="{{ $page->siteName }} Atom Feed">

    @if ($page->production && $page->gtm)
        <script>
            (function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
                const f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $page->gtm }}');
        </script>
    @endif

    <link rel="stylesheet" href="{{ mix('css/main.css', 'assets/build') }}">
</head>

<body class="flex flex-col justify-between min-h-screen leading-normal font-sans">
@if ($page->production && $page->gtm)
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ $page->gtm }}"
                height="0" width="0" style="display:none;visibility:hidden">
        </iframe>
    </noscript>
@endif
<header class="flex items-center shadow bg-white border-b h-24 py-4" role="banner">
    <div class="container flex items-center max-w-8xl mx-auto px-4 lg:px-8">
        <div class="flex items-center">
            <a href="/" title="{{ $page->siteName }} home" class="inline-flex items-center">
                <img class="h-20 h-20" src="/assets/img/logo.png" alt="{{ $page->siteName }} logo"/>
            </a>
        </div>

        <div id="vue-search" class="flex flex-1 justify-end items-center">
            @if($page->enableSearch)
                <search></search>
            @endif

            @include('_nav.menu')

            @include('_nav.menu-toggle')
        </div>
    </div>
</header>

@include('_nav.menu-responsive')

<main role="main" class="flex-auto w-full container max-w-4xl mx-auto py-16 px-6">
    @yield('body')
</main>

<footer class="bg-white text-center text-sm mt-12 py-4" role="contentinfo">
    <ul class="flex flex-col md:flex-row justify-center list-none">
        <li class="md:mr-2">
            &copy; {{ $page->siteName }} {{ date('Y') }}.
        </li>

        <li>
            Built with <a href="http://jigsaw.tighten.co" title="Jigsaw by Tighten">Jigsaw</a>
            and <a href="https://tailwindcss.com" title="Tailwind CSS, a utility-first CSS framework">Tailwind CSS</a>.
        </li>
    </ul>
</footer>

<script src="{{ mix('js/main.js', 'assets/build') }}"></script>

@stack('scripts')
</body>
</html>
