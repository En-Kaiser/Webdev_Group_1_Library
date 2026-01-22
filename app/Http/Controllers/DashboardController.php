<?php

namespace App\Http\Controllers;

use App\Models\bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class DashboardController extends Controller
{
    // == SHARED PAGES ==
    public function index()
    {
        if (!Auth::check()) {
            return view('dashboard.index');
        }

        return view('dashboard.index');
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

        $books = $query->select(
            'b.*',
            DB::raw('GROUP_CONCAT(DISTINCT a.name SEPARATOR ", ") as author'),
            DB::raw('GROUP_CONCAT(DISTINCT g.name SEPARATOR ", ") as genre')
        )
            ->groupBy('b.book_id')
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
        // Logic
        return view('dashboard.librarian.view');
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
            // This saves the file to storage/app/public/books
            // It returns a unique name
            $path = $request->file('cover_image')->store('books', 'public');
            $filename = basename($path);
        }

        DB::table('books')->insert([
            'title' => $request->title,
            'image' => $filename ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard.view')->with('success', 'Book added successfully!');
    }

    public function monitorUsers()
    {
        // Logic
        return view('dashboard.librarian.monitor_users');
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

    public function approve($id) {
        return redirect()->back()->with('success', 'Transaction approved!');
    }

    public function reject($id) {
        return redirect()->back()->with('success', 'Transaction rejected!');
    }
}
