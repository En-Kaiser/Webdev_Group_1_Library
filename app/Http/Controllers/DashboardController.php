<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // $books = DB::table('books as b')
        //     ->join('authors as a', 'a.author_id', '=', 'b.author_id')
        //     ->select('b.*', 'a.name as author_name')
        //     ->get();

        // $genres = DB::table('genres')->get();
        // $authors = DB::table('authors')->get();

        // return view('dashboard.index', compact('books', 'genres', 'authors'));

        return view('dashboard.index');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $books = DB::table('books as b')
            ->join('authors as a', 'a.author_id', '=', 'b.author_id')
            ->where('b.title', 'LIKE', "%{$searchTerm}%")
            ->orWhere('a.name', 'LIKE', "%{$searchTerm}%")
            ->select('b.*', 'a.name as author_name')
            ->get();

        $genres = DB::table('genres')->get();
        $authors = DB::table('authors')->get();

        return view('dashboard.index', compact('books', 'genres', 'authors', 'searchTerm'));
    }

    public function viewAll()
    {
        // $books = DB::table('books as b')
        //     ->join('authors as a', 'a.author_id', '=', 'b.author_id')
        //     ->select('b.*', 'a.name as author_name')
        //     ->orderBy('b.title', 'asc')
        //     ->get();

        // $genres = DB::table('genres')->get();
        // $authors = DB::table('authors')->get();

        // return view('dashboard.view', compact('books', 'genres', 'authors'));

        $books = collect([
            (object)['id' => 1, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 2, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 3, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 4, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 5, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 6, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 7, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 8, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 9, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 10, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 11, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 12, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
        ]);

        return view('dashboard.view', compact('books'));
    }

    public function bookmarked()
    {
        // To be implemented
        $books = collect([
            (object)['id' => 1, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 2, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 3, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 4, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 5, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 6, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 7, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 8, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 9, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 10, 'title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'genre' => 'Classic', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 11, 'title' => '1984', 'author' => 'George Orwell', 'genre' => 'Dystopian', 'cover_image' => 'book_sample.jpg'],
            (object)['id' => 12, 'title' => 'Harry Potter', 'author' => 'J.K. Rowling', 'genre' => 'Fiction', 'cover_image' => 'book_sample.jpg'],
        ]);

        return view('dashboard.bookmarked', compact('books'));
    }

    public function history()
    {
        // $userId = Auth::id();

        // $history = DB::table('history as h')
        //     ->join('books as b', 'b.book_id', '=', 'h.book_id')
        //     ->join('authors as a', 'a.author_id', '=', 'b.author_id')
        //     ->where('h.user_id', $userId)
        //     ->select(
        //         'h.*',
        //         'b.title',
        //         'b.image',
        //         'a.name as author_name'
        //     )
        //     ->orderBy('h.date_borrowed', 'desc')
        //     ->get();

        // return view('dashboard.history', compact('history'));

        $history = collect([
            (object)['title' => 'The Great Gatsby', 'type' => 'Physical', 'date_borrowed' => '01-01-2026', 'date_returned' => '01-07-2026', 'status' => 'Returned'],
            (object)['title' => '1984', 'type' => 'E-Book', 'date_borrowed' => '01-01-2026', 'date_returned' => '01-14-2026', 'status' => 'Returned'],
            (object)['title' => 'Brave New World',  'type' => 'Physical',  'date_borrowed' => '01-05-2026', 'date_returned' => null, 'status' => 'Borrowed'],   // Not returned yet
            (object)['title' => 'Pride and Prejudice', 'type' => 'E-Book', 'date_borrowed' => '12-15-2025', 'date_returned' => '12-22-2025', 'status' => 'Returned'],
            (object)['title' => 'To Kill a Mockingbird', 'type' => 'Physical', 'date_borrowed' => '01-10-2026', 'date_returned' => null, 'status' => 'Borrowed'],
            (object)['title' => 'The Catcher in the Rye', 'type' => 'Physical', 'date_borrowed' => '01-02-2026', 'date_returned' => '01-09-2026', 'status' => 'Returned'],
            (object)['title' => 'To Kill a Mockingbird', 'type' => 'Physical', 'date_borrowed' => '01-10-2026', 'date_returned' => null, 'status' => 'Borrowed'],
            (object)['title' => 'The Catcher in the Rye', 'type' => 'Physical', 'date_borrowed' => '01-02-2026', 'date_returned' => '01-09-2026', 'status' => 'Returned'],
        ]);

        return view('dashboard.history', compact('history'));
    }
}
