<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Inertia for server-driven single-page applications
use Inertia\Inertia;
// Import the Post model to interact with the posts table in the database
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        // Fetch published posts ordered by published date in descending order and paginate the results by 10 per page
        $posts = Post::where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        // Render the 'Blog/Index' view with the fetched posts
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show($slug)
    {
        // Fetch a single post by its slug where the published date is less than or equal to the current date. Take the first result or fail if not found (404).
        $post = Post::where('slug', $slug)
            ->where('published_at', '<=', now())
            ->firstOrFail();    
        
        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }
}

