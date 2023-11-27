<x-layout title="Projects" description="Open source software, hardware experiments, and other projects that I've worked on or am currently building.">
    <x-background></x-background>
    <header class="px-4 md:px-0">
        <h1 class="text-3xl lg:leading-relaxed font-semibold leading-snug text-gray-900 mt-8">
            Current and Past Projects
        </h1>
    </header>
    @foreach($projects as $project)
        <div class="leading-relaxed mb-4 md:mb-8 mt-4 bg-white border-0 md:border md:border-slate-200 md:border-b-slate-300 rounded-lg py-6 px-4 md:px-8">
            <div class="flex items-end">
                <div>
                    <x-categories plain>{{ $project->categories }}</x-categories>
                </div>
            </div>
            <a href="{{ $project->link }}" target="_blank" rel="noopener" class="inline-block text-xl font-semibold mt-3 transition-colors hover:text-slate-600">{{ $project->title }}</a>
            <p class="text-slate-700 mt-2 leading-loose">{{ $project->content }}</p>
            <p class="text-slate-700 mt-2 leading-loose"><a href="{{ $project->link }}" target="_blank" rel="noopener" class="text-sm font-semibold underline hover:no-underline">Check it out at {{ parse_url($project->link)['host'] }} &rarr;</a></p>
        </div>
    @endforeach
</x-layout>