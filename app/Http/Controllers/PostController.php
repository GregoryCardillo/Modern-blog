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
// Storage facade for file operations
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    
    // Display a listing of published posts
    public function index()
    {
        // Fetch published posts ordered by published date in descending order and paginate the results by 10 per page
        $posts = Post::where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        // Render the 'Blog/Index' view with the fetched posts
        /** @return \Inertia\Response */
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    // Display a single post based on its slug
    public function show($slug)
    {
        // Fetch a single post by its slug where the published date is less than or equal to the current date. Take the first result or fail if not found (404).
        $post = Post::where('slug', $slug)
            ->where('published_at', '<=', now())
            ->firstOrFail();    
        
        /** @return \Inertia\Response */
        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }

    // Show the form for creating a new post
    public function create()
    {
        /** @return \Inertia\Response */
        return Inertia::render('Blog/Create');
    }

    // Show the form for editing an existing post
    public function edit(Post $post)
    {
        /** @return \Inertia\Response */
        return Inertia::render('Blog/Edit', [
            'post' => $post,
        ]);
    }

    // Update an existing post in the database
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'sometimes|nullable|image|max:5120',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (is_array($file)) {
                $file = reset($file);
            }

            if ($file) {
                $path = $file->store('posts', 'public');

                // Remove the old image if it exists
                if (isset($post->image) && $post->image) {
                    Storage::disk('public')->delete($post->image);
                }

                $data['image'] = $path;
            }
        }

        // Update slug if title has changed
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Update the post with the validated data
        $post->update($data);

        /** @return \Illuminate\Http\RedirectResponse */
        return redirect()->route('blog.index')->with('success', 'Post updated successfully.');
    }

    // Delete a post from the database
    public function destroy(Post $post)
    {
        // Remove the associated image file if it exists
        if (isset($post->image) && $post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Actually delete the post
        $post->delete();

        /** @return \Illuminate\Http\RedirectResponse */
        return redirect()->route('blog.index')->with('success', 'Post deleted successfully.');
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
            $file = $request->file('image');

            if (is_array($file)) {
                $file = reset($file);
            }

            if ($file) {
                $validated['image'] = $file->store('posts', 'public');
            }
        }

        // If published_at is not provided, set it to the current date and time
        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Create a new post in the databse with the validated data
        $validated['user_id'] = $request->user()?->id;
        Post::create($validated);

        // Redirect back to the posts index with a success message
        return redirect()->route('blog.index')->with('success', 'Post created successfully.');
    }
}

