<?php

namespace App\Http\Controllers;

use App\Models\author;
use App\Models\book;
use App\Models\bookmark;
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
                'a.name as author',
                'a.author_id',
                'g.name as genre',
                'g.genre_id',
                DB::raw("CASE WHEN bta.type = 'physical' THEN 'Physical Book' WHEN bta.type = 'e_book' THEN 'E-Book' ELSE 'Physical Book' END as types"),
                'bta.type',
                'bta.availability'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('books.title', 'like', "%{$search}%")
                    ->orWhere('a.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $query->where('g.name', $request->genre);
        }

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
        $authors = Author::withCount('books')
            ->orderBy('name')
            ->get();

        $genres = Genre::withCount('books')
            ->orderBy('name')
            ->get();

        return view('dashboard.librarian.manage_authors_genres', compact('authors', 'genres'));
    }

    // --- MANAGE AVAILABILITY ---
    public function manageAvailability(Request $request)
    {
        $latestHistory = DB::table('history')
            ->select(
                'book_id',
                DB::raw('MAX(date_borrowed) as latest_borrow')
            )
            ->where('status', '!=', 'returned')
            ->groupBy('book_id');

        $query = DB::table('books as b')
            ->leftJoinSub($latestHistory, 'lh', function ($join) {
                $join->on('lh.book_id', '=', 'b.book_id');
            })
            ->leftJoin('history as h', function ($join) {
                $join->on('h.book_id', '=', 'b.book_id')
                    ->on('h.date_borrowed', '=', 'lh.latest_borrow');
            })
            ->select(
                'b.book_id',
                'b.title',
                DB::raw("COALESCE(h.status, 'available') as current_status")
            )
            ->groupBy('b.book_id', 'b.title', 'h.status');

        $books = $query->paginate(10);

        return view('dashboard.librarian.manage_availability', compact('books'));
    }

    public function updateStatus(Request $request, $bookId)
    {

        $request->validate([
            'status' => 'required|in:available,borrowed,due',
        ]);

        $updated = DB::table('history')
            ->where('book_id', $bookId)
            ->where('status', '!=', 'returned') 
            ->update([
                'status' => 'returned',
                'date_return' => now()
            ]);

        DB::table('book_type_avail')
            ->where('book_id', $bookId)
            ->update(['availability' => 'available']);

        if ($updated > 0) {
            return redirect()->route('manageAvailability')
                ->with('success', 'Book marked as returned and is now available.');
        } else {
            return redirect()->route('manageAvailability')
                ->with('info', 'No active borrow records found. Book is already available.');
        }
    }



    // ===== BOOKS =====
    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,author_id',
            'genre_id' => 'required|exists:genres,genre_id',
            'type' => 'required|in:physical,e_book',
            'status' => 'required|in:available,unavailable',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'short_description' => 'nullable|string',
        ]);

        // Handle cover image upload
        $imageName = null;
        if ($request->hasFile('cover_image')) {
            $filename = $request->file('cover_image')->store('books', 'public');
            $imageName = basename($filename);
        }

        // Insert book
        $bookId = DB::table('books')->insertGetId([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'year' => $request->year,
            'image' => $imageName,
        ]);

        // Link author & genre
        DB::table('books_joint_authors')->insert([
            'book_id' => $bookId,
            'author_id' => $request->author_id
        ]);

        DB::table('books_joint_genres')->insert([
            'book_id' => $bookId,
            'genre_id' => $request->genre_id
        ]);

        // Set type & availability
        DB::table('book_type_avail')->insert([
            'book_id' => $bookId,
            'type' => $request->type,
            'availability' => $request->status,
        ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book added successfully!');
    }

    public function updateBook(Request $request, $bookId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,author_id',
            'genre_id' => 'required|exists:genres,genre_id',
            'type' => 'required|in:physical,e_book',
            'status' => 'required|in:available,unavailable',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'short_description' => 'nullable|string',
        ]);

        $updateData = [
            'title' => $request->title,
            'short_description' => $request->short_description,
            'year' => $request->year,
        ];

        if ($request->hasFile('cover_image')) {
            $oldBook = DB::table('books')->where('book_id', $bookId)->first();
            if ($oldBook && $oldBook->image) {
                Storage::disk('public')->delete('books/' . $oldBook->image);
            }

            // Upload new image
            $filename = $request->file('cover_image')->store('books', 'public');
            $updateData['image'] = basename($filename);
        }

        // Update books table
        DB::table('books')
            ->where('book_id', $bookId)
            ->update($updateData);

        // Update author relationship
        DB::table('books_joint_authors')
            ->where('book_id', $bookId)
            ->update(['author_id' => $request->author_id]);

        // Update genre relationship
        DB::table('books_joint_genres')
            ->where('book_id', $bookId)
            ->update(['genre_id' => $request->genre_id]);

        // Update type & availability
        DB::table('book_type_avail')
            ->where('book_id', $bookId)
            ->update([
                'type' => $request->type,
                'availability' => $request->status,
            ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book updated successfully!');
    }

    // === ADD AUTHOR ===
    public function storeAuthor(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        author::create(['name' => $request->name]);

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Author added successfully!');
    }

    // === ADD GENRE ===
    public function storeGenre(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        genre::create(['name' => $request->name]);

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Genre added successfully!');
    }

    // ===== DELETE =====
    // Delete Author
    public function destroyAuthor($authorId)
    {
        $author = author::findOrFail($authorId);

        // Check if author has associated books
        if ($author->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete author with associated books.');
        }

        $author->delete();

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Author deleted successfully!');
    }

    // Delete Genre
    public function destroyGenre($genreId)
    {
        $genre = genre::findOrFail($genreId);

        // Check if genre has associated books
        if ($genre->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete genre with associated books.');
        }

        $genre->delete();

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Genre deleted successfully!');
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
