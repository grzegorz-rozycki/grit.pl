<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'grit',
    'siteDescription' => 'The Web is our passion',
    'siteAuthor' => 'Grzegorz',

    // feature flags
    'enableNewsletter' => false,
    'enableSearch' => true,

    'navItems' => [],

    'gtm' => '',

    'repositoryUrl' => 'https://github.com/grzegorz-rozycki/grit.pl',

    // collections
    'collections' => [
        'posts' => [
            'author' => 'Author Name', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'blog/{filename}',
        ],
        'categories' => [
            'path' => '/blog/categories/{filename}',
            'posts' => function ($page, $allPosts) {
                return $allPosts->filter(fn ($post) => $post->categories && in_array($page->getFilename(), $post->categories, true));
            },
        ],
    ],

    // helpers
    'getDate' => fn ($page) => DateTime::createFromFormat('U', (string)$page->date),

    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        $content = preg_split('/<!-- more -->/m', $page->getContent(), 2);
        $cleaned = trim(
            strip_tags(
                preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content[0]),
                '<code>'
            )
        );

        if (count($content) > 1) {
            return $cleaned;
        }

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . '...'
            : $cleaned;
    },
    'isActive' => fn ($page, $path) => Str::endsWith(trimPath($page->getPath()), trimPath($path)),
];
