<x-layout title="Projects">
    <x-background></x-background>
    <h1 class="text-3xl lg:text-4xl lg:leading-relaxed font-semibold leading-loose text-gray-900 mt-8">
        Current and Past Projects
    </h1>
    @foreach($projects as $project)
        <div class="leading-relaxed mb-8 mt-4 bg-white border border-slate-200 border-b-slate-300 rounded-lg py-6 px-8">
            <div class="flex items-end">
                <div>
                    <x-categories plain>{{ $project->categories }}</x-categories>
                </div>
            </div>
            <h2 class="inline-block text-xl font-semibold mt-3">{{ $project->title }}</h2>
            <p class="text-slate-700 mt-2 leading-loose">{{ $project->content }}</p>
        </div>
    @endforeach
</x-layout>