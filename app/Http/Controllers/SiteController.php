<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function posts(Request $request)
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

    public function post(Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'content' => Markdown::convert($post->content)->getContent()
        ]);
    }

    public function courses()
    {

    }

    public function projects()
    {
        return view('projects.index', [
            'projects' => Project::all()
        ]);
    }
}
