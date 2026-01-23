<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\SignUpController;
use App\Models\book;
use App\Models\user_account;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    $userCount = user_account::count();
    $bookCount = book::count();

    return view('welcome', compact('userCount', 'bookCount'));
})->name('welcome');

// == TESTING ==
Route::get('/test', [SignUpController::class, 'json_string']);
// == AUTH ==
Route::get('/signup', [SignUpController::class, 'showSignUp'])->name('auth.showSignUp');
Route::post('/signup', [SignUpController::class, 'signup'])->name('auth.signup');
Route::get('/login', [LogInController::class, 'showLogIn'])->name('auth.showLogIn');
Route::post('/login', [LogInController::class, 'login'])->name('auth.login');
Route::post('/logout', [LogInController::class, 'logout'])->name('auth.logout');

// == DASHBOARD (Open to Guests) ==
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('/all', [DashboardController::class, 'studentViewAll'])->name('student.viewAll');
    Route::get('/about', function () {return view('dashboard.aboutus');})->name('about');
});

// == BOOKS (Open to Guests) ==
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

// == STUDENT ROUTES - requires login ==
Route::middleware(['auth'])->group(function () {
    // borrowing
    Route::get('/books/{id}/borrow', [BookController::class, 'showBorrowPrompt'])->name('books.showBorrowPrompt');
    Route::post('/books/{id}/borrow', [BookController::class, 'borrow'])->name('books.borrow');
    Route::post('/books/{id}/bookmark', [BookController::class, 'bookmark'])->name('books.bookmark');
    // Student pages
    Route::get('/bookmarked', [DashboardController::class, 'bookmarked'])->name('student.bookmarked');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('student.history');
});

// == ADMIN/LIBRARIAN ROUTES - requires admin login ==
Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('librarian')->group(function () {
        Route::get('/all', [DashboardController::class, 'librarianViewAll'])->name('librarian.viewAll');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('librarian.transactions');
        Route::get('/create', [DashboardController::class, 'createSubmission'])->name('librarian.create');
        Route::post('/create', [DashboardController::class, 'store'])->name('librarian.store');
        // User monitoring
        Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::put('/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::post('/users/{id}/suspend', [AdminController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{id}/activate', [AdminController::class, 'activate'])->name('admin.users.activate');
        // Book management
        Route::get('/manage-books', [DashboardController::class, 'manageBooks'])->name('admin.manageBooks');

        // author-genre management
        Route::get('/manage-books/authors-genres', [DashboardController::class, 'manageAuthorsGenres'])->name('manageAuthorsGenres');
        Route::get('/manage-books/records', [DashboardController::class, 'manageRecords'])->name('manageRecords');

        Route::post('/authors', [DashboardController::class, 'storeAuthor'])->name('librarian.authors.store');
        Route::get('/authors', [DashboardController::class, 'listAuthors'])->name('librarian.authors.list');
        Route::delete('/authors/{id}', [DashboardController::class, 'destroyAuthor'])->name('librarian.authors.destroy');

        Route::post('/genres', [DashboardController::class, 'storeGenre'])->name('librarian.genres.store');
        Route::get('/genres', [DashboardController::class, 'listGenres'])->name('librarian.genres.list');
        Route::delete('/genres/{id}', [DashboardController::class, 'destroyGenre'])->name('librarian.genres.destroy');

        // book management
        Route::get('/books/create', [DashboardController::class, 'create'])->name('books.create');
        Route::post('/books', [DashboardController::class, 'storeBook'])->name('librarian.books.store');
        Route::get('/books/{id}/edit', [DashboardController::class, 'edit'])->name('books.edit');
        Route::put('/books/{bookId}', [DashboardController::class, 'updateBook'])->name('librarian.books.update');
        Route::put('/books/{book_id}/status', [DashboardController::class, 'updateStatus'])->name('librarian.updateStatus');
        Route::delete('/books/{id}', [DashboardController::class, 'destroyBook'])->name('librarian.books.destroy');

        // Transaction Actions
        Route::post('/transactions/approve/{id}', [DashboardController::class, 'approve'])->name('librarian.transactions.approve');
        Route::post('/transactions/reject/{id}', [DashboardController::class, 'reject'])->name('librarian.transactions.reject');
    });
});

Route::fallback(function () {
    return redirect()->route('welcome');
});
