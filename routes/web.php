<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Blog routes (public index & show)
use App\Http\Controllers\PostController;

Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
// NOTE: the `show` route is declared after admin/protected routes below

// Protected blog management routes (admin only)
Route::middleware(['auth','admin'])->group(function () {
    Route::get('/blog/create', [PostController::class, 'create'])->name('blog.create');
    Route::post('/blog', [PostController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [PostController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [PostController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [PostController::class, 'destroy'])->name('blog.destroy');
});

// Admin dashboard
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PostAdminController::class, 'index'])->name('dashboard');
    Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\PostAdminController::class, 'destroy'])->name('posts.destroy');
});

require __DIR__.'/auth.php';

// Public show route -- placed after protected routes so "create"/"edit" are not captured as slugs
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
