<?php

use App\Models\Post;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('landing', [
        'posts' => Post::latest('published_at')
            ->limit(5)
            ->get()
    ]);
});

Route::get('/blog', function (Request $request) {
    $posts = Post::latest('published_at')
        ->get();

    if ($request->has('category')) {
        $posts = $posts->filter(function ($post) use ($request) {
            $categories = explode(',', $post->categories);
            $categories = array_map('trim', $categories);

            return in_array($request->get('category'), $categories);
        });
    }

    return view('posts.index', [
        'posts' => $posts
    ]);
})->name('posts.index');

Route::get('/blog/{post:slug}', function (Post $post) {
    return view('posts.show', [
        'post' => $post,
        'content' => Markdown::convert($post->content)->getContent()
    ]);
})->name('posts.show');