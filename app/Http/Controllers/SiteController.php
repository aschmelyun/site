<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Project;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function posts(Request $request): View
    {
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
            'posts' => $posts,
            'request' => $request,
        ]);
    }

    public function post(Post $post): View
    {
        return view('posts.show', [
            'post' => $post,
            'content' => Markdown::convert($post->content)->getContent()
        ]);
    }

    public function courses(): View
    {
        return view('courses.index', [
            'courses' => Course::all()
        ]);
    }

    public function course(Course $course): View
    {
        return view('courses.show', [
            'course' => $course,
            'content' => Markdown::convert($course->content)->getContent()
        ]);
    }

    public function lesson(Course $course, Lesson $lesson)
    {
        return $lesson;
    }

    public function projects(): View
    {
        return view('projects.index', [
            'projects' => Project::all()
        ]);
    }
}
