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
        $full_info = DB::select("CALL books_info()");

        $bookRows = collect($full_info)->where('book_id', $id);
     
        if ($bookRows->isEmpty()) {
            abort(404, 'Book not found');
        }

        $book = $bookRows->first(); 

        $authors = $bookRows->pluck('author_name')->unique()->map(function($name) {
            return (object)['name' => $name];
        });

        $genres = $bookRows->pluck('genre_name')->unique()->map(function($name) {
            return (object)['name' => $name];
        });

        $book_type_avail = DB::table('book_type_avail')
                           ->where('book_id',$id)
                           ->get();
        $isAvailable = $book_type_avail->contains('availability', 'available');

        $isBookmarked = false;
        if (Auth::check()) {
            $isBookmarked = DB::table('bookmarks')
                ->where('user_id', Auth::id())
                ->where('book_id', $id)
                ->exists();
        }

        return view('books.show', compact('book', 'authors', 'genres', 'book_type_avail', 'isAvailable', 'isBookmarked'));
    }


    // For now made it so that user could only choose physical or e_book. Cannot be both
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


        DB::transaction(function () use ($userId, $id, $availability, $bookTypeId) {
            
    
            DB::table('history')->insert([
                'user_id' => $userId,
                'book_id' => $id,
                'type' => $availability -> type, 
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

        return redirect()->route('dashboard.history');
    }

    public function bookmark($id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.showLogIn');
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
