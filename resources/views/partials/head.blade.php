<head>
    <meta charset="utf-8">
    <title>{{ $title }} - Andrew Schmelyun</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $meta_title ?? $title }}" />
    <meta property="og:description" content="{{ $description ?? '' }}" />
    <meta property="og:url" content="https://aschmelyun.com{{ $path }}" />
    <meta property="og:site_name" content="Andrew Schmelyun" />
    @if($path !== '/' && file_exists(dirname(__FILE__, 2) . '/resources/assets/images/meta' . $path . '.jpg'))
        <meta name="og:image" content="https://aschmelyun.com/assets/images/meta{{ $path }}.jpg" />
    @else
        <meta property="og:image" content="https://aschmelyun.com/assets/images/meta/default.jpg" />
    @endif

    <meta name="twitter:description" content="{{ $description ?? '' }}" />
    <meta name="twitter:title" content="{{ $meta_title ?? $title }}" />
    <meta name="twitter:site" content="@aschmelyun" />
    @if($path !== '/' && file_exists(dirname(__FILE__, 2) . '/resources/assets/images/meta' . $path . '.jpg'))
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" content="https://aschmelyun.com/assets/images/meta{{ $path }}.jpg" />
    @else
        <meta name="twitter:card" content="summary" />
        <meta property="twitter:image" content="https://aschmelyun.com/assets/images/meta/default.jpg" />
    @endif

    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
    <meta name="theme-color" content="#1a202c">

    <link rel="stylesheet" type="text/css" href="{{ $mix['/assets/css/app.css'] }}">
    <script src="https://cdn.jsdelivr.net/npm/highlightjs-vue"></script>

    @if(isset($headScripts))
        @foreach($headScripts as $headScript)
            {!! $headScript !!}
        @endforeach
    @endif
</head>
