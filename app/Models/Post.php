<?php

namespace App\Models;

// Base Eloquent Model class
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $image
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int|null $user_id
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
// This code define a model Post that map the "posts" table in the database and protect the coloumns from mass assignment. 

class Post extends Model
{
    protected $fillable = [
        "title",
        'slug',
        'content',
        'image',
        'category',
        'published_at',
        'user_id',
    ];

// Cast published_at to a datetime object for easier date manipulations. 
// Create an associative array where 'published_at is the key and 'datetime' is the value. 
    /** @var array<string,string> */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * The post's author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}