<!DOCTYPE html>
<html lang="en-US">
@include('partials.head')
<body class="{{ $bodyClasses ?? '' }}">
<div class="max-w-4xl mx-auto pb-6 md:pb-12 antialiased">
    <div class="w-auto mx-4">
        @include('partials.nav')
        <h1 class="mt-8 mb-8 lg:mb-12 text-3xl lg:text-4xl text-gray-900 font-semibold section-heading relative"><span class="bg-white pr-3">All projects</span></h1>
        @foreach($projects as $project)
            <div class="mb-8">
                <h2 class="text-2xl title-font font-semibold text-gray-900 my-2"><a href="{{ $project->link }}" class="inline-block hover:underline" target="_blank" rel="noreferrer">{{ $project->name }}</a></h2>
                <p class="leading-relaxed mb-4 text-lg">{{ $project->description }}</p>
                <div class="flex items-center flex-wrap pb-8 mb-8 border-b-2 border-gray-100 mt-auto w-full">
                    <a href="{{ $project->link }}" target="_blank" rel="noreferrer" class="text-gray-900 inline-flex items-center hover:underline text-lg">Check It Out
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