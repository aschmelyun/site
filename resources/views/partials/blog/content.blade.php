<section class="text-gray-700 body-font overflow-hidden mt-4 lg:mt-16">
    <h1 class="text-4xl text-gray-900 font-semibold relative">{{ $title }}</h1>
    <div class="pt-4 pb-8 w-full flex flex-col items-start">
        <div class="mb-8 flex items-center">
            <span class="inline-block mr-4 text-gray-900 font-semibold">Published <time datetime="{{ date('Y-m-d H:i:s', strtotime($published)) }}">{{ $published }}</time></span>
            @foreach(explode(',', $categories) as $category)
                <span class="inline-block py-0.5 px-2 mr-2 rounded bg-white text-gray-700 border border-gray-600 text-xs font-semibold tracking-widest tag-{{ strtolower(trim($category)) }}">{{ strtoupper(trim($category)) }}</span>
            @endforeach
        </div>
        <div class="leading-relaxed blog-content w-full text-lg">{!! $body !!}</div>
    </div>
</section>