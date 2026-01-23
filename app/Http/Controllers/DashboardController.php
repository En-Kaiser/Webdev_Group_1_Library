<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\author;
use App\Models\book;
use App\Models\book_type_avail;
use App\Models\bookmark;
use App\Models\books_joint_author;
use App\Models\books_joint_genre;
use App\Models\genre;
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

        // If logged in, check role and redirect appropriately
        if (Auth::user()->role === 'librarian') {
            return view('dashboard.index'); // Shows librarian cards
        } else {
            return view('dashboard.index'); // Shows student cards
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

    // == STUDENT PAGES ==
    public function studentViewAll()
    {

        $books = DB::table('books as b')
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'b.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->select('b.*', 'a.name as author', 'g.genre_id', 'g.name as genre')
            ->get();


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


    // == LIBRARIAN PAGES == 
    public function librarianViewAll()
    {
        $books = book::query()
            ->with(['authors', 'genres'])
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'books.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'books.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->select('books.*', 'a.name as author', 'g.genre_id', 'g.name as genre')
            ->get();

        $genres = genre::all();

        return view('dashboard.librarian.view', compact('books', 'genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $filename = null;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books', 'public');
            $filename = basename($path);
        }

        book::create([
            'title' => $request->title,
            'image' => $filename,
        ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book added successfully!');
    }

    public function transactions()
    {
        // Dummy Pending Requests (Top section)
        $pendingRequests = collect([
            (object)[
                'id'     => 1,
                'type'   => 'Physical',
                'status' => 'Available',
                'user'   => (object)['first_name' => 'Juan', 'last_name' => 'Dela Cruz'],
                'book'   => (object)['title' => 'Introduction to Laravel']
            ],
            (object)[
                'id'     => 2,
                'type'   => 'E-Book',
                'status' => 'Unavailable',
                'user'   => (object)['first_name' => 'Maria', 'last_name' => 'Clara'],
                'book'   => (object)['title' => 'Data Structures and Algorithms']
            ],
            (object)[
                'id'     => 3,
                'type'   => 'Physical',
                'status' => 'Available',
                'user'   => (object)['first_name' => 'Jose', 'last_name' => 'Rizal'],
                'book'   => (object)['title' => 'Noli Me Tangere']
            ]
        ]);

        // Dummy Completed Transactions (Bottom table section)
        $completedTransactions = collect([
            (object)[
                'user_name'   => 'Cardo Dalisay',
                'book_title'  => 'Web Development 101',
                'type'        => 'Physical',
                'borrow_date' => '01-10-2026',
                'due_date'    => '01-17-2026',
                'return_date' => '01-17-2026',
                'status'      => 'Returned'
            ],
            (object)[
                'user_name'   => 'Cardo Dalisay',
                'book_title'  => 'Web Development 101',
                'type'        => 'Physical',
                'borrow_date' => '01-10-2026',
                'due_date'    => '01-17-2026',
                'return_date' => '01-17-2026',
                'status'      => 'Returned'
            ],
            (object)[
                'user_name'   => 'Niana Guerrero',
                'book_title'  => 'Modern Database Systems',
                'type'        => 'E-Book',
                'borrow_date' => '01-15-2026',
                'due_date'    => '01-17-2026',
                'return_date' => null,
                'status'      => 'Borrowed'
            ]
        ]);


        // == DB Connected Version: NEED TRANSACTION MODEL ==
        // $pendingRequests = Transaction::where('status', 'Pending')->get();

        // $completedTransactions = Transaction::whereIn('status', ['Borrowed', 'Returned'])
        //                                     ->orderBy('created_at', 'desc')
        //                                     ->get();

        return view('dashboard.librarian.transactions', compact('pendingRequests', 'completedTransactions'));
    }

    public function approve($id)
    {
        return redirect()->back()->with('success', 'Transaction approved!');
    }

    public function reject($id)
    {
        return redirect()->back()->with('success', 'Transaction rejected!');
    }

    // --- MANAGE BOOKS ---
    public function manageBooks(Request $request)
    {
        $query = book::query()
            ->with(['authors', 'genres', 'bookTypeAvail'])
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'books.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->join('books_joint_genres as bjg', 'bjg.book_id', '=', 'books.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjg.genre_id')
            ->leftJoin('book_type_avail as bta', 'bta.book_id', '=', 'books.book_id')
            ->select(
                'books.book_id',
                'books.title',
                'books.short_description',
                'books.image',
                'books.year',
                'bta.availability as status',
                DB::raw('GROUP_CONCAT(DISTINCT a.name) as author'),
                DB::raw('MIN(a.author_id) as author_id'),
                DB::raw('GROUP_CONCAT(DISTINCT g.name) as genre'),
                DB::raw('MIN(g.genre_id) as genre_id'),
                DB::raw("CASE WHEN bta.type = 'physical' THEN 'Physical Book' WHEN bta.type = 'e_book' THEN 'E-Book' ELSE 'Physical Book' END as types"),
                'bta.type',
                'bta.availability'
            )
            ->groupBy('books.book_id', 'books.title', 'books.short_description', 'books.image', 'books.year', 'bta.availability', 'bta.type')
            ->orderBy('books.title', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('books.title', 'like', "%{$search}%")
                    ->orWhere('a.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $query->having('genre', 'like', "%{$request->genre}%");
        }

        $query->orderBy('books.book_id', 'desc');
        $books = $query->paginate(10);
        $genres = genre::all();
        $authors = author::all();
        $searchTerm = $request->search;
        $selectedGenre = $request->genre;

        return view('dashboard.librarian.manage_books', compact('books', 'genres', 'authors', 'searchTerm', 'selectedGenre'));
    }

    // --- MANAGE AUTHORS & GENRES ---
    public function manageAuthorsGenres()
    {
        $authors = author::withCount('books')
            ->orderBy('name')
            ->get();

        $genres = genre::withCount('books')
            ->orderBy('name')
            ->get();

        return view('dashboard.librarian.manage_authors_genres', compact('authors', 'genres'));
    }

    // --- MANAGE Records ---
    public function manageRecords()
    {
        $books = DB::table('history')
            ->join('books', 'history.book_id', '=', 'books.book_id')
            ->join('user_accounts', 'history.user_id', '=', 'user_accounts.user_id')
            // ->where('history.status', 'borrowed')
            ->where('history.type', 'physical')
            ->select(
                'history.history_id',
                'books.title',
                'history.type',
                'user_accounts.first_name',
                'user_accounts.last_name',
                'history.date_borrowed',
                'history.status'
            )
            ->orderBy('history.date_borrowed', 'desc')
            ->paginate(10);;

        return view('dashboard.librarian.manage_records', compact('books'));
    }

    public function updateStatus(Request $request, $historyId)
    {

        $request->validate([
            'status' => 'required|in:borrowed,returned',
        ]);

        DB::table('history')
            ->where('history_id', $historyId)
            ->update([
                'status' => $request->status,
                'date_return' => $request->status === 'returned' ? now() : null
            ]);

        return redirect()->route('manageRecords')->with('success', 'Status updated successfully!');
    }

    // ===== BOOKS =====
    public function storeBook(BookRequest $request)
    {
        $imageName = null;
        if ($request->hasFile('cover_image')) {
            $filename = $request->file('cover_image')->store('books', 'public');
            $imageName = basename($filename);
        }

        $book = book::create([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'year' => $request->year,
            'image' => $imageName,
        ]);

        $book->authors()->attach($request->author_id);
        $book->genres()->attach($request->genre_id);

        book_type_avail::create([
            'book_id' => $book->book_id,
            'type' => $request->type,
            'availability' => $request->status,
        ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book added successfully!');
    }

    public function updateBook(BookRequest $request, $bookId)
    {
        $book = book::findOrFail($bookId);

        $book->title = $request->title;
        $book->short_description = $request->short_description;
        $book->year = $request->year;

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->image) {
                Storage::disk('public')->delete('books/' . $book->image);
            }

            // Upload new image
            $filename = $request->file('cover_image')->store('books', 'public');
            $book->image = basename($filename);
        }

        $book->save();

        books_joint_author::where('book_id', $bookId)
            ->update(['author_id' => $request->author_id]);

        books_joint_genre::where('book_id', $bookId)
            ->update(['genre_id' => $request->genre_id]);

        book_type_avail::where('book_id', $bookId)
            ->update([
                'type' => $request->type,
                'availability' => $request->status,
            ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book updated successfully!');
    }

    // == store author + genre
    public function storeAuthor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
        ]);

        author::create([
            'name' => $request->name,
        ]);

        return redirect()->route('manageAuthorsGenres')->with('success', 'Author added successfully!');
    }

    public function storeGenre(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ]);

        genre::create([
            'name' => $request->name,
        ]);

        return redirect()->route('manageAuthorsGenres')->with('success', 'Genre added successfully!');
    }

    // Delete Genre
    public function destroyGenre($genreId)
    {
        $genre = genre::findOrFail($genreId);

        if ($genre->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete genre with associated books.');
        }

        $genre->delete();

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Genre deleted successfully!');
    }

    // Delete author
    public function destroyAuthor($authorId)
    {
        $author = author::findOrFail($authorId);

        if ($author->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete author with associated books.');
        }

        $author->delete();

        return redirect()->route('manageAuthorsGenres')->with('success', 'Author deleted successfully!');
    }

    // Delete Books
    public function destroyBook($bookId)
    {
        $book = book::findOrFail($bookId);

        $book->bookTypeAvail()->delete();
        $book->authors()->detach();
        $book->genres()->detach();

        $book->delete();

        return redirect()->route('admin.manageBooks')->with('success', 'Book deleted successfully!');
    }
}
