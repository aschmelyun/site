<!DOCTYPE html>
<html lang="en-US">
@include('partials.head')
<body class="{{ $bodyClasses ?? '' }}">
<div class="container mx-auto pb-6 md:pb-12 antialiased">
    <div class="w-auto mx-4 lg:w-4/5 lg:mx-auto">
        @include('partials.nav')
        <h1 class="mt-8 mb-8 lg:mb-12 text-3xl lg:text-4xl text-gray-900 font-medium section-heading relative"><span class="bg-white pr-3">Published videos</span></h1>
        @php
            $videos = collect($videos)->sortByDesc(function($video, $index) {
                return strtotime($video->published);
            });
        @endphp
        @foreach($videos as $video)
        <div class="flex flex-wrap -mx-4 mb-8 border-b-2 border-gray-100">
            <div class="sm:w-2/5 mb-4 lg:mb-8 px-4">
                <a href="https://youtube.com/watch?v={{ $video->id }}" target="_blank" rel="noreferrer">
                    <img class="hover:opacity-75 transition-opacity duration-200" src="https://img.youtube.com/vi/{{ $video->id }}/maxresdefault.jpg">
                </a>
            </div>
            <div class="sm:w-3/5 mb-8 px-4">
                <h2 class="text-2xl title-font font-medium text-gray-900 my-2"><a href="https://youtube.com/watch?v={{ $video->id }}" class="inline-block hover:underline" target="_blank" rel="noreferrer">{{ $video->title }}</a></h2>
                <p class="text-gray-600 leading-relaxed mb-4"><time class="text-gray-900 font-medium" datetime="{{ date('Y-m-d H:i:s', strtotime($video->published)) }}">{{ $video->published }}</time> &mdash; {{ $video->description }}</p>
                <a href="https://youtube.com/watch?v={{ $video->id }}" target="_blank" rel="noreferrer" class="text-gray-900 inline-flex items-center hover:underline">Watch Now
                    <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="M12 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        @endforeach
        @include('partials.footer')
    </div>
</div>
</body>
</html>