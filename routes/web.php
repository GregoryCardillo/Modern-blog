<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Blog routes (public)
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/create', [PostController::class, 'create'])->name('blog.create');
Route::post('/blog', [PostController::class, 'store'])->name('blog.store');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');

require __DIR__.'/settings.php';
