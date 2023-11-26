<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="HandheldFriendly" content="True">

  <title>{{ $title ?? 'Home' }} - Andrew Schmelyun</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=dm-serif-display:400|poppins:400,400i,600,700,700i" rel="stylesheet" />

  @vite('resources/css/app.css')

  <meta name="description" content="{{ $description ?? '' }}">

  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="{{ $title ?? '' }}" />
  <meta property="og:description" content="{{ $description ?? '' }}" />
  <meta property="og:url" content="{{ url()->current() }}/" />
  <meta property="og:site_name" content="Andrew Schmelyun" />
  @if (url()->current() === env('APP_URL'))
<meta property="og:image" content="{{ env('APP_URL') . '/assets/images/meta/home' }}.jpg" />
  @else
<meta property="og:image" content="{{ str_replace(env('APP_URL'), env('APP_URL') . '/assets/images/meta', url()->current()) }}.jpg" />
  @endif

  <meta name="twitter:title" content="{{ $title ?? '' }}" />
  <meta name="twitter:description" content="{{ $description ?? '' }}" />
  <meta name="twitter:site" content="@aschmelyun" />
  <meta name="twitter:card" content="summary_large_image" />
  @if (url()->current() === env('APP_URL'))
<meta property="twitter:image" content="{{ env('APP_URL') . '/assets/images/meta/home' }}.jpg" />
  @else
<meta property="twitter:image" content="{{ str_replace(env('APP_URL'), env('APP_URL') . '/assets/images/meta', url()->current()) }}.jpg" />
  @endif

  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">

  <meta name="theme-color" content="#1a202c">

  @if (isset($post))
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "{{ $post->title }}",
    "image": "{{ env('APP_URL') . '/assets/images/blog/' . $post->slug . '.jpg' }}",
    "author": {
      "@type": "Person",
      "name": "Andrew Schmelyun",
      "url": "https://aschmelyun.com"
    },
    "publisher": {
      "@type": "Person",
      "name": "Andrew Schmelyun",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ env('APP_URL') . '/assets/images/andrew-schmelyun-profile.jpg' }}"
      }
    },
    "datePublished": "{{ date('Y-m-d', strtotime($post->published_at)) }}",
    "dateModified": "{{ date('Y-m-d', strtotime($post->modified_at ?? $post->published_at)) }}"
  }
</script>
  @endif

  @if(env('APP_ENV') === 'production')
<script src="https://cdn.usefathom.com/script.js" data-site="CWKFJVNZ" defer></script>
  @endif

</head>

<body class="antialiased">
  <div class="max-w-4xl mx-auto pb-6 md:pb-12 text-gray-900">
    <x-header></x-header>
    {{ $slot }}
    <x-footer></x-footer>
  </div>
</body>

</html>