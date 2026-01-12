<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function show($id)
    {
        $dummyBooks = [
            1 => [
                'title' => 'It Ends With Us',
                'author_name' => 'Colleen Hoover',
                'year' => '2016',
                'description' => 'It Ends with Us is a romance novel by Colleen Hoover, published by Atria Books on August 2, 2016. The story follows florist Lily Bloom, whose abusive relationship with neurosurgeon Ryle Kincaid is compounded when her high school boyfriend Atlas Corrigan re-enters her life.',
                'language' => 'English',
                'isbn' => '978-0262033848',
                'genres' => ['Fiction', 'Romance novel'],
                'cover_image' => 'pup_book01.png',
                'physical_copies' => 3,
                'ebook_copies' => 10,
            ],
            2 => [
                'title' => 'Fundamentals of Information Technology',
                'author_name' => ' Shambhavi Roy, Clinton Daniel, and Manish Agrawal',
                'year' => '2008',
                'description' => 'Even bad code can function. But if code isn’t clean, it can bring a development organization to its knees.',
                'language' => 'English',
                'isbn' => '978-0132350884',
                'genres' => ['Software Engineering', 'Programming'],
                'cover_image' => 'pup_book02.png',
                'physical_copies' => 0,
                'ebook_copies' => 0,
            ],
        ];

        if (!isset($dummyBooks[$id])) {
            abort(404);
        }

        $data = $dummyBooks[$id];

        $book = (object) array_merge($data, [
            'book_id' => $id,
            'author_id' => 1,
        ]);

        $genres = collect($data['genres'])->map(function ($name, $index) {
            return (object) ['genre_id' => $index + 1, 'name' => $name];
        });

        // ✅ Build availability from dummy data
        $availability = collect([
            (object) ['type' => 'Physical', 'copies_available' => $data['physical_copies']],
            (object) ['type' => 'E-book', 'copies_available' => $data['ebook_copies']],
        ]);

        // ✅ Check if ANY copy type has stock
        $isAvailable = $availability->contains(fn($item) => $item->copies_available > 0);

        $isLoggedIn = true;
        $isBookmarked = ($id == 1);

        return view('books.show', compact(
            'book',
            'genres',
            'availability',
            'isLoggedIn',
            'isBookmarked',
            'isAvailable'
        ));


        // $book = DB::table('books as b')
        //     ->join('authors as a', 'a.author_id', '=', 'b.author_id')
        //     ->where('b.book_id', $id)
        //     ->select('b.*', 'a.name as author_name', 'a.author_id')
        //     ->first();

        // $genres = DB::table('books_joint_genres as bjg')
        //     ->join('genres as g', 'g.genre_id', '=', 'bjg.genre_id')
        //     ->where('bjg.book_id', $id)
        //     ->select('g.*')
        //     ->get();

        // $availability = DB::table('book_type_avail')
        //     ->where('book_id', $id)
        //     ->get();

        // $isLoggedIn = Auth::check();

        // // Check if user has bookmarked this book (only if logged in)
        // $isBookmarked = false;
        // if ($isLoggedIn) {
        //     $isBookmarked = DB::table('user_joint_books')
        //         ->where('user_id', Auth::id())
        //         ->where('book_id', $id)
        //         ->where('type', 'bookmarked')
        //         ->exists();
        // }

        // return view('books.show', compact('book', 'genres', 'availability', 'isLoggedIn', 'isBookmarked'));
    }

    // possible mabago since idk pa anong nasa borrow view
    public function showBorrowPrompt($id)
    {
        if (!Auth::check()) {
            return redirect('/signup'); // if not logged in, redirect to sign up
        }

        $book = DB::table('books as b')
            ->join('authors as a', 'a.author_id', '=', 'b.author_id')
            ->where('b.book_id', $id)
            ->select('b.*', 'a.name as author_name')
            ->first();

        // Get only available copies
        $availability = DB::table('book_type_avail')
            ->where('book_id', $id)
            ->where('availability', 'available')
            ->get();

        if ($availability->isEmpty()) {
            return redirect("/books/{$id}"); // still subject to change as additional pop-up features might be integrated
        }

        return view('books.borrow', compact('book', 'availability'));
    }

    public function borrow(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/signup'); // if not logged in, redirect to sign up
        }

        $userId = Auth::id();
        $availTypeId = $request->input('avail_type_id');

        $availability = DB::table('book_type_avail')
            ->where('avail_type_id', $availTypeId)
            ->where('book_id', $id)
            ->where('availability', 'available')
            ->first();

        if (!$availability) {
            return redirect("/books/{$id}");  // still subject to change as additional pop-up features might be integrated
        }

        $alreadyBorrowed = DB::table('history')
            ->where('user_id', $userId)
            ->where('book_id', $id)
            ->where('status', 'borrowed')
            ->whereNull('date_return')
            ->exists();

        if ($alreadyBorrowed) {
            return redirect("/books/{$id}"); // still subject to change as additional pop-up features might be integrated
        }

        // Create borrowing history record
        DB::table('history')->insert([
            'user_id' => $userId,
            'book_id' => $id,
            'date_borrowed' => now(),
            'status' => 'borrowed',
            'transaction_type' => $availability->type
        ]);

        DB::table('book_type_avail')
            ->where('avail_type_id', $availTypeId)
            ->update(['availability' => 'borrowed']);

        return redirect('/dashboard/history');
    }

    public function returnBook($historyId)
    {
        // to be implemented
    }

    public function toggleBookmark($id)
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
