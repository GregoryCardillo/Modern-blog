<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;

class PostPolicy
{
    /**
     * Anyone (including guests) can view the list of posts.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone (including guests) can view a single post.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Only admins can create posts.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can update posts.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can delete posts.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}