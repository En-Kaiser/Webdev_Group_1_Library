<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\Testing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// == TESTING ==
Route::get('/test', [SignUpController::class, 'json_string']);
// == AUTH ==
Route::get('/signup', [SignUpController::class, 'showSignUp'])->name('auth.showSignUp');
Route::post('/signup', [SignUpController::class, 'signup'])->name('auth.signup');
Route::get('/login', [LogInController::class, 'showLogIn'])->name('auth.showLogIn');
Route::post('/login', [LogInController::class, 'login'])->name('auth.login');

// == DASHBOARD ==
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/search', [DashboardController::class, 'search'])->name('dashboard.search');
    Route::get('/all', [DashboardController::class, 'viewAll'])->name('dashboard.viewAll');
    Route::get('/bookmarked', [DashboardController::class, 'bookmarked'])->name('dashboard.bookmarked');
});

// == BOOKS ==
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

// == requires login ==
Route::middleware(['check.login'])->group(function () {
    // borrowing
    Route::get('/books/{id}/borrow', [BookController::class, 'showBorrowPrompt'])->name('books.showBorrowPrompt');
    Route::post('/books/{id}/borrow', [BookController::class, 'borrow'])->name('books.borrow');
    // history
    // Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');
});

Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');

Route::fallback(function () {
    return redirect()->route('welcome');
});
