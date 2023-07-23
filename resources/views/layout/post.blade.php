<!DOCTYPE html>
<html lang="en-US">
@include('partials.head')
<body class="{{ $bodyClasses ?? '' }}">
<div class="max-w-4xl mx-auto pb-6 md:pb-12 antialiased">
    <div class="w-auto mx-4">
        @include('partials.nav')
        @include('partials.blog.content')
        @include('partials.blog.newsletter')
        @include('partials.footer')
    </div>
</div>
</body>
</html>