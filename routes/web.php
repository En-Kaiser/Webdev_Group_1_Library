<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\Testing;
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

// == requires login ==
Route::middleware(['auth'])->group(function () {
    // borrowing
    Route::get('/books/{id}/borrow', [BookController::class, 'showBorrowPrompt'])->name('books.showBorrowPrompt');
    Route::post('/books/{id}/borrow', [BookController::class, 'borrow'])->name('books.borrow');

    // Student 
    Route::get('/bookmarked', [DashboardController::class, 'bookmarked'])->name('student.bookmarked');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('student.history');

    // Librarian
    Route::group(['prefix' => 'librarian'], function () {
        Route::get('/all', [DashboardController::class, 'librarianViewAll'])->name('librarian.viewAll');
        Route::get('/create', [DashboardController::class, 'createSubmission'])->name('librarian.create');
        Route::post('/create', [DashboardController::class, 'store'])->name('librarian.store');
        Route::get('/monitor-users', [DashboardController::class, 'monitorUsers'])->name('librarian.monitorUsers');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('librarian.transactions');

        // HANNA - me naglagay ng mga ituh
        Route::get('/manage-books', [DashboardController::class, 'manageBooks'])->name('manageBooks');
        Route::get('/manage-books/authors-genres', [DashboardController::class, 'manageAuthorsGenres'])->name('manageAuthorsGenres');
        Route::get('/manage-books/availability', [DashboardController::class, 'manageAvailability'])->name('manageAvailability');

        Route::post('/authors', [DashboardController::class, 'storeAuthor'])->name('librarian.authors.store');
        Route::get('/authors', [DashboardController::class, 'listAuthors'])->name('librarian.authors.list');
        Route::delete('/authors/{id}', [DashboardController::class, 'destroyAuthor'])->name('librarian.authors.destroy');

        Route::post('/genres', [DashboardController::class, 'storeGenre'])->name('librarian.genres.store');
        Route::get('/genres', [DashboardController::class, 'listGenres'])->name('librarian.genres.list');
        Route::delete('/genres/{id}', [DashboardController::class, 'destroyGenre'])->name('librarian.genres.destroy');

        Route::get('/books/create', [DashboardController::class, 'create'])->name('books.create');
         Route::post('/books', [DashboardController::class, 'storeBook'])->name('librarian.books.store');
        Route::get('/books/{id}/edit', [DashboardController::class, 'edit'])->name('books.edit');
        Route::put('/books/{id}', [DashboardController::class, 'updateBook'])->name('librarian.books.update');
        Route::put('/books/{book_id}/status', [DashboardController::class, 'updateStatus'])->name('librarian.updateStatus');
        Route::delete('/books/{id}', [DashboardController::class, 'destroyBook'])->name('librarian.books.destroy');

    
    });
});

Route::fallback(function () {
    return redirect()->route('welcome');
});
