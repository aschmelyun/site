<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing', [
        'posts' => Post::latest('published_at')
            ->limit(5)
            ->get()
    ]);
});

Route::get('/blog', function () {
    return view('posts.index', [
        'posts' => Post::latest('published_at')
            ->get()
    ]);
});
