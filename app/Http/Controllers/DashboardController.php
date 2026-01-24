<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\admin_history;
use App\Models\author;
use App\Models\book;
use App\Models\book_type_avail;
use App\Models\bookmark;
use App\Models\books_joint_author;
use App\Models\books_joint_genre;
use App\Models\genre;
use App\Models\history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Symfony\Component\Clock\now;

class DashboardController extends Controller
{
    // == SHARED PAGES ==
    public function index()
    {
        if (!Auth::check()) {
            return view('dashboard.index');
        }

        if (Auth::user()->role === 'librarian') {
            return view('dashboard.index');
        } else {
            return view('dashboard.index');
        }
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $books = DB::table('books as b')
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'b.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->where('b.title', 'LIKE', "%{$searchTerm}%")
            ->orWhere('a.name', 'LIKE', "%{$searchTerm}%")
            ->select('b.*', 'a.name as author', 'g.genre_id', 'g.name as genre')
            ->get();


        $genres = DB::table('genres')->get();

        return view('dashboard.student.view', compact('books', 'genres', 'searchTerm'));
    }

    public function aboutUs()
    {
        return view('dashboard.aboutus');
    }

    public function studentViewAll(Request $request)
    {
        $selectedGenre = $request->query('genre');

        $query = DB::table('books as b')
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'b.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id');

        if ($selectedGenre && $selectedGenre !== 'all') {
            $query->where('g.name', '=', $selectedGenre);
        }

        $books = $query->select('b.*', 'a.name as author', 'g.genre_id', 'g.name as genre')->get();

        $genres = DB::table('genres')->get();

        return view('dashboard.student.view', compact('books', 'genres'));
    }

    public function bookmarked(Request $request)
    {
        $query = DB::table('books as b')
            ->join('bookmarks as bm', 'bm.book_id', '=', 'b.book_id')
            ->leftJoin('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->leftJoin('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->leftJoin('books_joint_genres as bjg', 'bjg.book_id', '=', 'b.book_id')
            ->leftJoin('genres as g', 'g.genre_id', '=', 'bjg.genre_id')
            ->where('bm.user_id', '=', Auth::id());

        if ($request->has('genre') && $request->genre !== 'all') {
            $query->where('g.name', '=', $request->genre);
        }

        $books_concat = $query->select(
            'b.book_id',
            DB::raw('GROUP_CONCAT(DISTINCT a.name SEPARATOR ", ") as author'),
            DB::raw('GROUP_CONCAT(DISTINCT g.name SEPARATOR ", ") as genre')
        )
            ->groupBy('b.book_id');

        $books = DB::table('books AS b')
            ->joinSub($books_concat, 'bc', function ($join) {
                $join->on('b.book_id', '=', 'bc.book_id');
            })
            ->select('b.*', 'bc.author', 'bc.genre')
            ->get();


        $genres = DB::table('genres')->get();

        return view('dashboard.student.bookmarked', compact('books', 'genres'));
    }

    public function history(Request $request)
    {
        $query = DB::table('history as h')
            ->join('books as b', 'b.book_id', '=', 'h.book_id')
            ->where('h.user_id', '=', Auth::id());

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('h.status', '=', $request->status);
        }

        $history = $query->select(
            'h.history_id',
            'b.title',
            'h.type',
            'h.status',
            'h.date_borrowed',
            'h.date_return'
        )
            ->orderBy('h.date_borrowed', 'desc')
            ->get();

        return view('dashboard.student.history', compact('history'));
    }

    // == LIBRARIAN PAGE == 
    public function librarianViewAll(Request $request)
    {
        $selectedGenre = $request->input('genre');

        $query = Book::query()
            ->with(['authors', 'genres']);

        if ($selectedGenre && $selectedGenre !== 'all') {
            $query->whereHas('genres', function ($q) use ($selectedGenre) {
                $q->where('name', $selectedGenre);
            });
        }

        $books = $query->get();

        $books->transform(function ($book) {
            $book->author = $book->authors->pluck('name')->join(', ');
            $book->genre = $book->genres->pluck('name')->join(', ');
            $book->genre_id = $book->genres->first()->genre_id ?? null;
            return $book;
        });

        $genres = Genre::all();

        return view('dashboard.librarian.view', compact('books', 'genres', 'selectedGenre'));
    }
}
