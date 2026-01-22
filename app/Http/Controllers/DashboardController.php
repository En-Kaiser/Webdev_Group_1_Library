<?php

namespace App\Http\Controllers;

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
        $books = DB::table('books as b')
            ->join('books_joint_genres as bjb', 'bjb.book_id', '=', 'b.book_id')
            ->join('genres as g', 'g.genre_id', '=', 'bjb.genre_id')
            ->join('books_joint_authors as bja', 'bja.book_id', '=', 'b.book_id')
            ->join('authors as a', 'a.author_id', '=', 'bja.author_id')
            ->select('b.*', 'a.name as author', 'g.genre_id', 'g.name as genre')
            ->get();


        $genres = DB::table('genres')->get();

        return view('dashboard.librarian.view', compact('books', 'genres'));
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

    public function monitorUsers()
    {
        // Logic
        return view('dashboard.librarian.monitor_users');
    }

    public function transactions()
    {
        // 
        return view('dashboard.librarian.transactions');
    }



    // ===== HANNA =====

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
                'b.book_id',
                'b.title',
                'b.short_description',
                'b.image',
                'b.year',
                DB::raw("COALESCE(current_status.current_status, 'Available') as status"),
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
                $q->where('b.title', 'like', "%{$search}%")
                    ->orWhere('a.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('genre')) {
            $query->where('g.name', $request->genre);
        }

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
        $authors = DB::table('authors as a')
            ->leftJoin('books_joint_authors as bja', 'a.author_id', '=', 'bja.author_id')
            ->select(
                'a.author_id',
                'a.name',
                DB::raw('COUNT(bja.book_id) as books_count')
            )
            ->groupBy('a.author_id', 'a.name')
            ->get();

        $genres = DB::table('genres as g')
            ->leftJoin('books_joint_genres as bjg', 'g.genre_id', '=', 'bjg.genre_id')
            ->select(
                'g.genre_id',
                'g.name',
                DB::raw('COUNT(bjg.book_id) as books_count')
            )
            ->groupBy('g.genre_id', 'g.name')
            ->get();

        return view('dashboard.librarian.manage_authors_genres', compact('authors', 'genres'));
    }

    // --- MANAGE AVAILABILITY ---
    public function manageAvailability()
    {
        $books = DB::table('books as b')
            ->leftJoin('book_type_avail as bta', 'bta.book_id', '=', 'b.book_id')
            ->select(
                'b.book_id',
                'b.title',
                'bta.type',
                'bta.availability as status'
            )
            ->get();

        return view('dashboard.librarian.manage_availability', compact('books'));
    }

    public function updateStatus(Request $request, $bookId)
    {
        $request->validate([
            'status' => 'required|in:available,unavailable',
        ]);

        DB::table('book_type_avail')
            ->where('book_id', $bookId)
            ->update(['availability' => $request->status]);

        return redirect()->route('manageAvailability')->with('success', 'Status updated successfully!');
    }

    // ===== BOOKS =====
    public function storeBook(Request $request)
    {
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

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
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

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

    // ===== AUTHOR =====
    public function storeAuthor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
        ]);

        DB::table('authors')->insertGetId([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success', 'Author added successfully!');
    }

    // ===== GENRE =====
    public function storeGenre(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ]);

        DB::table('genres')->insertGetId([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('success', 'Genre added successfully!');
    }


    // ===== DELETE =====
    // Delete Author
    public function destroyAuthor($authorId)
    {
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $bookCount = DB::table('books_joint_authors')
            ->where('author_id', $authorId)
            ->count();

        if ($bookCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete author with associated books.');
        }

        DB::table('authors')->where('author_id', $authorId)->delete();

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Author deleted successfully!');
    }

    // Delete Genre
    public function destroyGenre($genreId)
    {
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $bookCount = DB::table('books_joint_genres')
            ->where('genre_id', $genreId)
            ->count();

        if ($bookCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete genre with associated books.');
        }

        DB::table('genres')->where('genre_id', $genreId)->delete();

        return redirect()->route('manageAuthorsGenres')
            ->with('success', 'Genre deleted successfully!');
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

        return redirect()->route('manageBooks')->with('success', 'Book deleted successfully!');
    }
}
