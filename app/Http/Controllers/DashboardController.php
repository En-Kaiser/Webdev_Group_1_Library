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
use App\Models\history;
use App\Models\bookmark;
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

    // == STUDENT PAGES ==
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

    // == LIBRARIAN PAGES == 
    public function librarianViewAll(Request $request)
    {
        $selectedGenre = $request->input('genre');

        $query = book::query()
            ->with(['authors', 'genres'])
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'books.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->select('books.*', 'a.name as author', 'g.genre_id', 'g.name as genre')
            ->distinct();

        if ($selectedGenre && $selectedGenre !== 'all') {
            $query->where('g.name', $selectedGenre);
        }

        $books = $query->get();
        $genres = genre::all();

        return view('dashboard.librarian.view', compact('books', 'genres', 'selectedGenre'));
    }

    public function createSubmission(Request $request)
    {
        if (Auth::user()->role !== 'librarian') {
            return redirect()->route('dashboard.index')->with('error', 'Unauthorized access.');
        }

        return view('dashboard.librarian.create_submission');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max2048',
        ]);

        $filename = null;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books', 'public');
            $filename = basename($path);
        }

        DB::table('books')->insert([
            'title' => $request->title,
            'image' => $filename ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('manageBooks')->with('success', 'Book added successfully!');
    }

   

    // --- MANAGE BOOKS ---
    public function manageBooks(Request $request)
    {
        $bookStatusQuery = DB::table('history as h')
            ->select('h.book_id', DB::raw("'Borrowed' as current_status"))
            ->where('h.status', 'borrowed')
            ->whereNull('h.date_return')
            ->groupBy('h.book_id');

        $query = DB::table('books as b')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->join('books_joint_genres as bjg', 'bjg.book_id', '=', 'b.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjg.genre_id')
            ->leftJoinSub($bookStatusQuery, 'current_status', function ($join) {
                $join->on('current_status.book_id', '=', 'b.book_id');
            })
            ->leftJoin('book_type_avail as bta', 'bta.book_id', '=', 'b.book_id')
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
                $q->where('b.title', 'like', "%{$search}%")
                    ->orWhere('a.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $query->having('genre', 'like', "%{$request->genre}%");
        }

        $query->orderBy('books.book_id', 'desc');
        $books = $query->paginate(10);
        $genres = DB::table('genres')->get();
        $authors = DB::table('authors')->get();
        $searchTerm = $request->search;
        $selectedGenre = $request->genre; // Add this line

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
        $books = history::with(['book', 'user'])
            ->where('type', 'physical')
            ->orderBy('date_borrowed', 'desc')
            ->paginate(10);

        return view('dashboard.librarian.manage_records', compact('books'));
    }

    public function updateStatus(Request $request, $historyId)
    {
        $request->validate([
            'status' => 'required|in:borrowed,returned',
        ]);

        $history = history::findOrFail($historyId);

        $history->update([
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
            $file = $request->file('cover_image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('books'), $imageName);
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

        DB::table('admin_history')->insert([
            'admin_id'      => Auth::id(),        
            'book_id'       => $bookId,     
            'description'   => "New book added: " . $request->title,         
            'change_created'=> now(),             
        ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book added successfully!');
    }

    public function updateBook(BookRequest $request, $bookId)
    {
        $book = book::findOrFail($bookId);

        $book->title = $request->title;
        $book->short_description = $request->short_description;
        $book->year = $request->year;

        $oldBook = DB::table('books')->where('book_id', $bookId)->first();
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            if ($oldBook && $oldBook->image) {
                $oldPath = public_path('books/' . $oldBook->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $file->move(public_path('books'), $filename);
            $updateData['image'] = $filename;
        }

        $book->save();

        books_joint_author::where('book_id', $bookId)
            ->update(['author_id' => $request->author_id]);

        books_joint_genre::where('book_id', $bookId)
            ->update(['genre_id' => $request->genre_id]);

        book_type_avail::where('book_id', $bookId)
            ->update([
                'availability' => $request->status,
            ]);

            DB::table('admin_history')->insert([
                'admin_id'       => Auth::id(),
                'user_id'        => NULL,
                'book_id'        => $bookId,
                'description'    => "Updated book: ". $request -> title,
                'change_created' => now()
                 
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
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        if ($genre->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete genre with associated books.');
        }

        DB::table('genres')->where('genre_id', $genreId)->delete();

        DB::table('admin_history')->insert([
            'admin_id'       => Auth::id(),
            'description'    => "Deleted Genre: ". $genreId,
            'change_created' => now()
        ]);
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
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        DB::table('book_type_avail')->where('book_id', $bookId)->delete();
        DB::table('books_joint_authors')->where('book_id', $bookId)->delete();
        DB::table('books_joint_genres')->where('book_id', $bookId)->delete();

        DB::table('books')->where('book_id', $bookId)->delete();
        DB::table('admin_history')->insert([
            'admin_id'       => Auth::id(),
            'book_id'        => $bookId,
            'description'    => "Book Deleted",
            'change_created' => now()
        ]);
        return redirect()->route('manageBooks')->with('success', 'Book deleted successfully!');
    }
}
