<section class="text-gray-600 body-font overflow-hidden mt-16">
    <h2 class="mb-8 lg:mb-12 text-3xl lg:text-4xl text-gray-900 font-medium section-heading relative"><span class="bg-white pr-3">Latest posts</span></h2>
    @php
        $latestPosts = $cleaver->filter(function($post, $key) {
            return $post->view === 'layout.post';
        })->sortByDesc(function($post, $key) {
            return strtotime($post->published);
        })->take(3)->all();
    @endphp
    @foreach($latestPosts as $post)
        @include('partials.blog.preview', ['post' => $post])
    @endforeach
</section>