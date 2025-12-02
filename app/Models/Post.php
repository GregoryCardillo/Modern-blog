<?php

namespace App\Models;

// Base Eloquent Model class
use Illuminate\Database\Eloquent\Model;

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
    ];

// Cast published_at to a datetime object for easier date manipulations. 
// Create an associative array where 'published_at is the key and 'datetime' is the value. 
    protected $casts = [
        'published_at' => 'datetime',
    ];

}