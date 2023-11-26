<x-layout :post="$post">
    <x-background></x-background>
    <header>
        <h1 class="text-3xl lg:leading-relaxed font-semibold leading-loose text-gray-900 mt-8">
            {{ $post->title }}
        </h1>
        <div class="flex items-end">
            <div>
                <x-categories>{{ $post->categories }}</x-categories>
            </div>
            <div class="ml-2 text-slate-600">
                <span class="text-sm">{{ $post->published_at }}</span>
            </div>
        </div>
    </header>
    <main class="mt-8 leading-relaxed mb-8 mt-4 bg-white rounded-lg py-8 px-8">
        <div class="prose prose-lg mx-auto">
            {!! $content !!}

            <div>
                <div class="flex items-center justify-between">
                    <h2>My Newsletter</h2>
                    <a href="https://aschmelyun.substack.com/latest" target="_blank" rel="noopener">Read sample</a>
                </div>
                <p class="mt-0">Subscribe using the form below and about 1-2 times a month you'll receive an email containing helpful hints, new packages, and interesting articles I've found on PHP, JavaScript, Docker and more.</p>
                <div>
                    <iframe src="https://aschmelyun.substack.com/embed" class="h-24 overflow-hidden" width="100%" height="320" style="border:0px; background:#F3F4F6;" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>
        </div>
    </main>
</x-layout>