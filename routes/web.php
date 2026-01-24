<?php

use App\Http\Controllers\AuthorGenreController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogInController;
use App\Http\Controllers\MonitorUsersController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\TransactionController;
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
Route::get('/download-book', [BookController::class, 'downloadEbook'])->name('books.download.itds')->middleware('auth');
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
    // Route::get('/about', function () {return view('dashboard.aboutus');})->name('about');
    Route::get('/about', [DashboardController::class, 'aboutUs'])->name('aboutUs');
});

// == BOOKS (Open to Guests) ==
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

// == STUDENT ROUTES - requires login ==
Route::middleware(['auth'])->group(function () {
    // borrowing
    // Route::get('/books/{id}/borrow', [BookController::class, 'showBorrowPrompt'])->name('books.showBorrowPrompt');
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
        Route::get('/transactions', [TransactionController::class, 'transactions'])->name('librarian.transactions');

        // User monitoring
        Route::get('/users', [MonitorUsersController::class, 'index'])->name('admin.users.index');
        Route::put('/users/{id}', [MonitorUsersController::class, 'update'])->name('admin.users.update');
        Route::post('/users/{id}/suspend', [MonitorUsersController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{id}/activate', [MonitorUsersController::class, 'activate'])->name('admin.users.activate');

        // author-genre management
        Route::get('/manage-books/authors-genres', [AuthorGenreController::class, 'manageAuthorsGenres'])->name('manageAuthorsGenres');
        Route::post('/authors', [AuthorGenreController::class, 'storeAuthor'])->name('librarian.authors.store');
        Route::delete('/authors/{id}', [AuthorGenreController::class, 'destroyAuthor'])->name('librarian.authors.destroy');
        Route::post('/genres', [AuthorGenreController::class, 'storeGenre'])->name('librarian.genres.store');
        Route::delete('/genres/{id}', [AuthorGenreController::class, 'destroyGenre'])->name('librarian.genres.destroy');

        // Book management
        Route::get('/manage-books', [BookManagementController::class, 'manageBooks'])->name('admin.manageBooks');
        Route::post('/books', [BookManagementController::class, 'storeBook'])->name('librarian.books.store');
        Route::put('/books/{book_id}/status', [BookManagementController::class, 'updateStatus'])->name('librarian.updateStatus');
        Route::delete('/books/{id}/{type}', [BookManagementController::class, 'destroyBook'])->name('librarian.books.destroy');
        Route::get('/manage-books/records', [BookManagementController::class, 'manageRecords'])->name('manageRecords');
        Route::put('/books/{bookId}', [BookManagementController::class, 'updateBook'])->name('librarian.books.update');

        // Transaction Actions
        Route::post('/transactions/approve/{id}', [TransactionController::class, 'approve'])->name('librarian.transactions.approve');
        Route::post('/transactions/reject/{id}', [TransactionController::class, 'reject'])->name('librarian.transactions.reject');
    });
});

Route::fallback(function () {
    return redirect()->route('welcome');
});
