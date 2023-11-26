<x-layout :post="null" title="Blog" description="Read along and learn more about PHP, Laravel, JavaScript, Vue, and Docker.">
    <x-background></x-background>
    <header class="px-4 md:px-0">
        <h1 class="text-3xl lg:leading-relaxed font-semibold leading-snug text-gray-900 mt-8">
            {{ $request->has('category') ? ucfirst($request->get('category')) : 'All' }} Blog Posts
        </h1>
    </header>
    @foreach($posts as $post)
        <div class="leading-relaxed mb-4 md:mb-8 mt-4 bg-white border-0 md:border md:border-slate-200 md:border-b-slate-300 rounded-lg py-6 px-4 md:px-8">
            <div class="flex items-end">
                <div>
                    <x-categories>{{ $post->categories }}</x-categories>
                </div>
                <div class="ml-2 text-slate-600">
                    <span class="text-sm">{{ $post->published_at->format('M j, Y') }}</span>
                </div>
            </div>
            <a href="{{ route('posts.show', $post->slug) }}" class="inline-block text-xl font-semibold mt-3 transition-colors hover:text-slate-600">{{ $post->title }}</a>
            <p class="text-slate-700 mt-2 leading-loose">{{ $post->excerpt }}</p>
            <p class="text-slate-700 mt-2 leading-loose"><a href="/blog/{{ $post->slug }}" class="text-sm font-semibold underline hover:no-underline">Read more &rarr;</a></p>
        </div>
    @endforeach
</x-layout>