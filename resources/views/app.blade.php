<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>{{ $seo['title'] }}</title>

    <meta
        name="description"
        content="{{ $seo['description'] }}"
    >

    <meta
        name="keywords"
        content="{{ $seo['keywords'] }}"
    >

    <meta name="robots" content="{{ $seo['robots'] }}">

    <meta
        name="google-site-verification"
        content="9QYipKtRQWppOEXb3Om70Pr_jtQryPAnrhxV0b09ZHs"
    >

    <meta property="og:site_name" content="hiroshima-live">
    <meta property="og:type" content="{{ $seo['type'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:locale" content="ja_JP">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    <link
        rel="canonical"
        href="{{ $seo['url'] }}"
    >

    <link
        rel="icon"
        type="image/png"
        href="/favicon.png"
    >

    <link
        rel="apple-touch-icon"
        href="/favicon.png"
    >

    <script type="application/ld+json">
        {!! json_encode($seo['structuredData'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    @vite('resources/js/app.js')
</head>

<body class="bg-zinc-950">
    <div id="app"></div>
</body>
</html>
