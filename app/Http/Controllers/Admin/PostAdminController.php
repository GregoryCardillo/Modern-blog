<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostAdminController extends Controller
{
    use AuthorizesRequests;

    /** @return \Inertia\Response */
    public function index(Request $request)
    {
        $posts = Post::with('user')->latest()->paginate(20)->through(function ($post) use ($request) {
            return [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->title,
                'user' => $post->user ? ['id' => $post->user->id, 'name' => $post->user->name] : null,
                'published_at' => $post->published_at,
                'can' => [
                    'update' => $request->user()?->can('update', $post) ?? false,
                    'delete' => $request->user()?->can('delete', $post) ?? false,
                ],
            ];
        });

        return Inertia::render('Admin/Dashboard', ['posts' => $posts]);
    }

    /** @return \Illuminate\Http\RedirectResponse */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Post deleted.');
    }
}
