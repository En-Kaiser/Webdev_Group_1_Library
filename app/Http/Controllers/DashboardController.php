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
        $bookStatusQuery = history::query()
            ->select('book_id', DB::raw("'Borrowed' as current_status"))
            ->where('status', 'borrowed')
            ->whereNull('date_return')
            ->groupBy('book_id');

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
            'book_id'       => $book->book_id,
            'description'   => "New book added: " . $request->title,
            'change_created' => now(),
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
            'description'    => "Updated book: " . $request->title,
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
        $genre = genre::findOrFail($genreId);

        if ($genre->books()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete genre with associated books.');
        }

        $genreName = $genre->name;
        $genre->delete();

        admin_history::create([
            'admin_id'       => Auth::id(),
            'description'    => "Deleted Genre: {$genreName}",
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

        $book = Book::findOrFail($bookId);

        $bookTitle = $book->title;

        $book->authors()->detach();
        $book->genres()->detach();
        $book->bookTypeAvail()->delete();

        admin_history::create([
            'admin_id'       => Auth::id(),
            'book_id'        => $bookId,
            'description'    => "Deleted Book: {$bookTitle}",
            'change_created' => now()
        ]);

        $book->delete();

        return redirect()->route('admin.manageBooks')->with('success', 'Book deleted successfully!');
    }
}
