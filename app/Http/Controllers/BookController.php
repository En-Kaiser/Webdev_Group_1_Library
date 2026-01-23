<?php

namespace App\Http\Controllers;

use App\Models\book_type_avail;
use App\Models\bookmark;
use App\Models\books_joint_author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function show($id)
    {
        $bookRows = DB::table('books as b')
            ->select(
                'b.*',
                'bta.availability',
                'a.name as author_name',
                'g.name as genre_name'
            )
            ->leftJoin('book_type_avail as bta', 'b.book_id', '=', 'bta.book_id')
            ->leftJoin('books_joint_authors as bja', 'b.book_id', '=', 'bja.book_id')
            ->leftJoin('authors as a', 'bja.author_id', '=', 'a.author_id')
            ->leftJoin('books_joint_genres as bjg', 'b.book_id', '=', 'bjg.book_id')
            ->leftJoin('genres as g', 'bjg.genre_id', '=', 'g.genre_id')
            ->where('b.book_id', $id)
            ->get();


        if ($bookRows->isEmpty()) {
            abort(404, 'Book not found');
        }

        $book = $bookRows->first();

        $authors = $bookRows->pluck('author_name')->unique()->map(function ($name) {
            return (object)['name' => $name];
        });

        $genres = $bookRows->pluck('genre_name')->unique()->map(function ($name) {
            return (object)['name' => $name];
        });

        $book_type_avail = DB::table('book_type_avail')
            ->where('book_id', $id)
            ->get();
        $isAvailable = $book_type_avail->contains('availability', 'available');

        $isBookmarked = false;
        if (Auth::check()) {
            $isBookmarked = DB::table('bookmarks')
                ->where('user_id', Auth::id())
                ->where('book_id', $id)
                ->exists();
        }

        // HANNA - Added it for next next ng book
        $bookIds = DB::table('books')->orderBy('book_id')->pluck('book_id');

        $currentKey = $bookIds->search($id);

        $prevId = $currentKey > 0 ? $bookIds[$currentKey - 1] : null;
        $nextId = $currentKey !== false && $currentKey < $bookIds->count() - 1
            ? $bookIds[$currentKey + 1]
            : null;

        return view('books.show', compact('book', 'authors', 'genres', 'book_type_avail', 'isAvailable', 'isBookmarked', 'prevId','nextId'));
    }

    public function borrow(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.showLogIn');
        }

        $request->validate([
            'book_type_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $bookTypeId = $request->input('book_type_id');

        $availability = DB::table('book_type_avail')
            ->where('book_type_id', $bookTypeId)
            ->where('book_id', $id)
            ->where('availability', 'available')
            ->first();

        if (!$availability) {
            return redirect()->route('books.show', $id)
                ->with('error', 'This copy is currently unavailable.');
        }

        // Check if user already borrowed this specific book
        $alreadyBorrowed = DB::table('history')
            ->where('user_id', $userId)
            ->where('book_id', $id)
            ->where('status', 'borrowed')
            ->whereNull('date_return')
            ->exists();

        if ($alreadyBorrowed) {
            return redirect()->route('books.show', $id)
                ->with('error', 'You have already borrowed this book.');
        }

        // Process transaction
        DB::transaction(function () use ($userId, $id, $availability, $bookTypeId) {
            DB::table('history')->insert([
                'user_id' => $userId,
                'book_id' => $id,
                'type' => $availability->type,
                'date_borrowed' => now(),
                'date_return' => NULL,
                'status' => 'borrowed',
            ]);


            if ($availability->type === 'physical') {
                DB::table('book_type_avail')
                    ->where('book_type_id', $bookTypeId)
                    ->update(['availability' => 'unavailable']);
            }
        });

        return back()->with('success', 'Book borrowed successfully!');
    }

    public function showBorrowPrompt($id) 
    {
        $book = DB::table('books')->where('book_id', $id)->first();

        if (!$book) {
            abort(404);
        }

        return view('books.borrow_confirm', compact('book'));
    }

    public function bookmark($id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.showSignUp');
        }

        $userId = Auth::id();

        $exists = DB::table('bookmarks')
            ->where('user_id', $userId)
            ->where('book_id', $id)
            ->exists();

        if ($exists) {
            DB::table('bookmarks')
                ->where('user_id', $userId)
                ->where('book_id', $id)
                ->delete();
        } else {
            DB::table('bookmarks')->insert([
                'user_id' => $userId,
                'book_id' => $id
            ]);
        }

        return back();
    }

    public function returnBook($historyId)
    {
        // to be implemented
    }

    public function byAuthor($authorId)
    {
        // to be implemented
    }

    public function byGenre($genreId)
    {
        // to be implemented
    }


}
