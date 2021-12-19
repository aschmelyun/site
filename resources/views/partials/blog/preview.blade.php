<section class="text-gray-600 body-font overflow-hidden">
    <div class="w-full flex flex-col items-start">
        <div>
            @foreach(explode(',', $post->categories) as $category)
                <span class="inline-block py-0.5 px-2 mr-1 rounded bg-white text-gray-600 border border-gray-600 text-xs font-medium tracking-widest tag-{{ strtolower(trim($category)) }}">{{ strtoupper(trim($category)) }}</span>
            @endforeach
        </div>
        <h2 class="text-2xl title-font font-medium text-gray-900 my-2"><a href="{{ $post->path }}" class="inline-block hover:underline">{{ $post->title }}</a></h2>
        <p class="leading-relaxed mb-4">{!! $post->excerpt !!}</p>
        <div class="flex items-center flex-wrap pb-8 mb-8 border-b-2 border-gray-100 mt-auto w-full">
            <a href="{{ $post->path }}" class="text-gray-900 inline-flex items-center hover:underline">Read More
                <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>