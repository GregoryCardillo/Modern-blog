<?php

namespace App\Http\Controllers;

// HTTP Request class for handling incoming requests
use Illuminate\Http\Request;
// Inertia for server-driven single-page applications
use Inertia\Inertia;
// Import the Post model to interact with the posts table in the database
use App\Models\Post;
// Str helper for string manipulations
use Illuminate\Support\Str;


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

    // Store a newly created post in the database
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'published_at' => 'nullable|date',
        ]);

        // Generate a unique slug from the title
        $validated['slug'] = Str::slug($validated['title'] . '-' . time());

        // If an image is uploaded, handle the file upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // If published_at is not provided, set it to the current date and time
        if (!isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Create a new post in the databse with the validated data
        Post::create($validated);

        // Redirect back to the posts index with a success message
        return redirect()->route('blog.index')->with('succes', 'Post created successfully.');
    }
}

