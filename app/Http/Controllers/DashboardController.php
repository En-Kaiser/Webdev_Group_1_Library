<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $books = DB::table('books as b')
            ->join('authors as a', 'a.author_id', '=', 'b.author_id')
            ->select('b.*', 'a.name as author_name')
            ->get();

        $genres = DB::table('genres')->get();
        $authors = DB::table('authors')->get();

        return view('dashboard.index', compact('books', 'genres', 'authors'));
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
        $books = DB::table('books as b')
            ->join('authors as a', 'a.author_id', '=', 'b.author_id')
            ->select('b.*', 'a.name as author_name')
            ->orderBy('b.title', 'asc')
            ->get();

        $genres = DB::table('genres')->get();
        $authors = DB::table('authors')->get();

        return view('dashboard.view', compact('books', 'genres', 'authors'));
    }

    public function bookmarked()
    {
        // To be implemented
    }

    public function history()
    {
        $userId = Auth::id();

        $history = DB::table('history as h')
            ->join('books as b', 'b.book_id', '=', 'h.book_id')
            ->join('authors as a', 'a.author_id', '=', 'b.author_id')
            ->where('h.user_id', $userId)
            ->select(
                'h.*',
                'b.title',
                'b.image',
                'a.name as author_name'
            )
            ->orderBy('h.date_borrowed', 'desc')
            ->get();

        return view('dashboard.history', compact('history'));
    }
}
