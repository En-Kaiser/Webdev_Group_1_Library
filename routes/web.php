<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\Testing;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
Route::post('/logout', [LogInController::class, 'logout'])->name('auth.logout');

// == DASHBOARD (Open ato Guests) ==
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('/all', [DashboardController::class, 'studentViewAll'])->name('student.viewAll');
});

// == BOOKS (Open to Guests) ==
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
Route::post('/books/{id}', [BookController::class, 'borrow'])->name('books.borrow');
Route::post('/books/{id}', [BookController::class, 'bookmark'])->name('books.bookmark');

// == requires login ==
Route::middleware(['auth'])->group(function () {
    // borrowing
    Route::get('/books/{id}/borrow', [BookController::class, 'showBorrowPrompt'])->name('books.showBorrowPrompt');
    Route::post('/books/{id}/borrow', [BookController::class, 'borrow'])->name('books.borrow');

    // Student 
    Route::get('/bookmarked', [DashboardController::class, 'bookmarked'])->name('student.bookmarked');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('student.history');

    // Librarian
    // Route::group(['prefix' => 'librarian'], function () {
    //     Route::get('/all', [DashboardController::class, 'librarianViewAll'])->name('librarian.viewAll');
    //     Route::get('/create', [DashboardController::class, 'createSubmission'])->name('librarian.create');
    //     Route::post('/create', [DashboardController::class, 'store'])->name('librarian.store');
    //     Route::get('/monitor-users', [DashboardController::class, 'monitorUsers'])->name('librarian.monitorUsers');
    //     Route::get('/transactions', [DashboardController::class, 'transactions'])->name('librarian.transactions');
    // });
    // Admin Routes - User Management
    Route::prefix('librarian')->group(function () {
        Route::get('/dash', [DashboardController::class, 'libDash'])->name('librarian.dashboard');
        Route::get('/all', [DashboardController::class, 'librarianViewAll'])->name('librarian.viewAll');
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('librarian.transactions');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
    });
});

// // Admin Routes - User Management
// Route::prefix('admin')->group(function () {
//     Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
//     Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
//     Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
//     Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
// });

Route::fallback(function () {
    return redirect()->route('welcome');
});
