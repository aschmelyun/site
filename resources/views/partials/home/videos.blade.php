<section class="text-gray-600 body-font overflow-hidden mt-4 lg:mt-12">
    <h2 class="mb-8 lg:mb-12 text-3xl lg:text-4xl text-gray-900 font-medium section-heading relative"><span class="bg-white pr-3">Recent videos</span></h2>
    <div class="flex flex-wrap -mx-4 text-center">
        @php
            $recentVideos = collect($videos)->sortByDesc(function($video, $index) {
                return strtotime($video->published);
            })->take(2)->all();
        @endphp
        @foreach($recentVideos as $video)
            <div class="w-full md:w-1/2 mb-8 px-4">
                <iframe width="100%" height="279" src="https://www.youtube.com/embed/{{ $video->id }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        @endforeach
    </div>
</section>