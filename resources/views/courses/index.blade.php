<x-layout title="Courses" description="Here's a list of my free and premium courses that I've created to help power-up your web development game.">
    <x-background></x-background>
    <header class="px-4 md:px-0">
        <h1 class="text-3xl lg:leading-relaxed font-semibold leading-snug text-gray-900 mt-8">
            My Courses
        </h1>
    </header>
    @foreach($courses as $course)
        <div class="leading-relaxed mb-4 md:mb-8 mt-4 bg-white border-0 md:border md:border-slate-200 md:border-b-slate-300 rounded-lg py-6 px-4 md:px-8">
            <div class="flex items-end">

            </div>
            <div class="flex items-center">
                <div class="w-1/4 mr-8">
                    <img class="max-w-full" src="{{ $course->thumbnail }}">
                </div>
                <div class="w-3/4">
                    <div>
                        <x-categories plain>{{ $course->categories }}</x-categories>
                    </div>
                    <a href="{{ $course->link }}" target="_blank" rel="noopener" class="inline-block text-xl font-semibold mt-3 transition-colors hover:text-slate-600">{{ $course->title }}</a>
                    <p class="text-slate-700 mt-2 leading-loose">{{ $course->content }}</p>
                    <p class="text-slate-700 mt-2 leading-loose"><a href="{{ $course->link }}" target="_blank" rel="noopener" class="text-sm font-semibold underline hover:no-underline">Check it out at {{ parse_url($course->link)['host'] }} &rarr;</a></p>
                </div>
            </div>
        </div>
    @endforeach
</x-layout>