<?php

use App\Http\Controllers\SiteController;
use App\Http\Middleware\AddTrailingSlash;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing', [
        'posts' => Post::latest('published_at')
            ->limit(5)
            ->get()
    ]);
});

Route::group(['middleware' => AddTrailingSlash::class], function () {

    Route::get('/blog', [SiteController::class, 'posts'])
        ->name('posts.index');

    Route::get('/blog/{post:slug}', [SiteController::class, 'post'])
        ->name('posts.show');

    Route::get('/courses/', [SiteController::class, 'courses'])
        ->name('courses.index');

    Route::get('/courses/{course}', [SiteController::class, 'course'])
        ->name('courses.show');

    Route::get('/courses/{course}/{lesson}', [SiteController::class, 'lesson'])
        ->name('lessons.show');

    Route::get('/projects', [SiteController::class, 'projects'])
        ->name('projects.index');

});