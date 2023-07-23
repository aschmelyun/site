<!DOCTYPE html>
<html lang="en-US">
@include('partials.head')
<body class="{{ $bodyClasses ?? '' }}">
<div class="max-w-4xl mx-auto pb-6 md:pb-12 antialiased">
    <div class="w-auto mx-8">
        @include('partials.nav')
        @foreach($sections as $section)
        <h1 class="mt-8 mb-4 text-3xl lg:text-4xl text-gray-900 font-semibold section-heading relative"><span class="bg-white pr-3">{{ $section->name }}</span></h1>
        <div class="flex flex-wrap -mx-4 mb-8">
            @foreach($section->items as $item)
                <div class="p-4 md:w-1/3">
                    <div class="h-full border-2 border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <h2 class="title-font text-lg font-semibold text-gray-900 mb-3"><a href="{{ $item->link }}" class="inline-block hover:underline" target="_blank" rel="noreferrer">{{ $item->name }}</a></h2>
                            <p class="leading-relaxed mb-3 text-lg text-gray-700">{{ $item->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endforeach
        @include('partials.footer')
    </div>
</div>
</body>
</html>