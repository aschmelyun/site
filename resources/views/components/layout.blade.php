<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Home' }} - Andrew Schmelyun</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400|poppins:400,400i,600,700,700i" rel="stylesheet" />

    @vite('resources/css/app.css')
</head>
<body class="antialiased">
    <div class="max-w-4xl mx-auto pb-6 md:pb-12 text-gray-900">
        <x-header></x-header>
        {{ $slot }}
        <x-footer></x-footer>
    </div>
</body>
</html>