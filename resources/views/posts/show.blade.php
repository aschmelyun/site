<x-layout :post="$post" :title="$post->title" :description="$post->description">
    <x-background></x-background>
    <header class="px-4 md:px-0">
        <h1 class="text-3xl lg:leading-snug font-semibold leading-snug text-gray-900 mt-8 mb-2">
            {{ $post->title }}
        </h1>
        <div class="flex items-end mt-4 md:mt-0">
            <div>
                <x-categories>{{ $post->categories }}</x-categories>
            </div>
            <div class="ml-2 text-slate-600">
                <span class="text-sm">{{ $post->published_at->format('M j, Y') }}</span>
            </div>
        </div>
    </header>
    <main class="mt-8 leading-relaxed mb-8 mt-4 bg-white rounded-lg py-8 px-4 md:px-8">
        <div class="prose md:prose-lg prose-h2:text-2xl prose-h2:mb-4 mx-auto">
            {!! $content !!}

            <div>
                <div class="flex items-center justify-between">
                    <h2>My Newsletter</h2>
                    <a href="https://aschmelyun.substack.com/latest" target="_blank" rel="noopener" class="hidden md:inline">Read sample</a>
                </div>
                <p class="mt-0">Subscribe using the form below and about 1-2 times a month you'll receive an email containing helpful hints, new packages, and interesting articles I've found on PHP, JavaScript, Docker and more.</p>
                <div class="mt-8">
                    <script async src="https://eocampaign1.com/form/61927954-2aee-11f1-8527-b3f9765ac105.js" data-form="61927954-2aee-11f1-8527-b3f9765ac105"></script>
                </div>
            </div>
        </div>
    </main>
</x-layout>
