<x-layout>
    <x-background></x-background>
    <h1 class="text-3xl lg:text-4xl lg:leading-relaxed font-semibold leading-loose text-gray-900 mt-8">
        All Blog Posts
    </h1>
    @foreach($posts as $post)
        <div class="leading-relaxed mb-8 mt-4 bg-white border border-slate-200 border-b-slate-300 rounded-lg py-6 px-8">
            <div class="flex items-end">
                <div>
                    <x-categories>{{ $post->categories }}</x-categories>
                </div>
            </div>
            <a href="{{ route('posts.show', $post->slug) }}" class="inline-block text-xl font-semibold mt-3 transition-colors hover:text-slate-600">{{ $post->title }}</a>
            <p class="text-slate-700 mt-2 leading-loose">{{ $post->excerpt }} <a class="whitespace-nowrap underline hover:no-underline hover:text-gray-900" href="/blog/{{ $post->slug }}">Read More &rarr;</a></p>
        </div>
    @endforeach
</x-layout>