<!DOCTYPE html>
<html lang="en-US">
@include('partials.head')
<body class="{{ $bodyClasses ?? '' }}">
<div class="container mx-auto pb-6 md:pb-12 antialiased">
    <div class="w-auto mx-4 lg:w-4/5 lg:mx-auto">
        @include('partials.nav')
        @include('partials.home.intro')
        @include('partials.home.articles')
        @include('partials.footer')
    </div>
</div>
</body>
</html>