<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\admin_history;
use App\Models\author;
use App\Models\book;
use App\Models\book_type_avail;
use App\Models\books_joint_author;
use App\Models\books_joint_genre;
use App\Models\genre;
use App\Models\history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BookManagementController extends Controller
{
    
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
            )->orderBy('books.title', 'asc');;

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

    // Add book
    public function storeBook(BookRequest $request)
    {
        $imageName = null;

        // Add picture 
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('books'), $imageName);
        }

        $existingBook = book::where('title', $request->title)
            ->where('year', $request->year)
            ->first();
        /* If book_id exists, check if specific type
           also exists, if true then go back else create as usual*/
        if ($existingBook) {
            $typeExists = book_type_avail::where('book_id', $existingBook->book_id)
                ->where('type', $request->type)
                ->exists();

            if ($typeExists) {
                return redirect()->back()
                    ->withErrors(['type' => 'This book type already exists. A book cannot have duplicate types.'])
                    ->withInput();
            }

            book_type_avail::create([
                'book_id' => $existingBook->book_id,
                'type' => $request->type,
                'availability' => $request->status,
            ]);
            
            // Log to admin history changes
            DB::table('admin_history')->insert([
                'admin_id'      => Auth::id(),
                'book_id'       => $existingBook->book_id,
                'description'   => "Added {$request->type} type to book: " . $request->title,
                'change_created' => now(),
            ]);

            return redirect()->route('admin.manageBooks')
                ->with('success', 'Book type added successfully to existing book!');
        }
        // If book_id does not exist, create 1 physical and e_book type
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

        if (strcasecmp($request->type, 'Physical') == 0) {
            $vice_versa = 'e_book';
        } else {
            $vice_versa = 'physical';
        }
       
        book_type_avail::create([
            'book_id' => $book->book_id,
            'type' => $vice_versa,
            'availability' => 'Unavailable',
        ]);

        DB::table('admin_history')->insert([
            'admin_id'      => Auth::id(),
            'book_id'       => $book->book_id,
            'description'   => "New book added: " . $request->title,
            'change_created' => now(),
        ]);

        return redirect()->route('admin.manageBooks')
            ->with('success', 'Book added successfully!');
    }

    // Update book
    public function updateBook(BookRequest $request, $bookId)
    {
        
        $book = book::findOrFail($bookId);

        $book->title = $request->title;
        $book->short_description = $request->short_description;
        $book->year = $request->year;

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Update image path
            if ($book->image) {
                $oldPath = public_path('books/' . $book->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file->move(public_path('books'), $filename);

            $book->image = $filename;
        }

        $book->save();

        books_joint_author::where('book_id', $bookId)
            ->update(['author_id' => $request->author_id]);

        books_joint_genre::where('book_id', $bookId)
            ->update(['genre_id' => $request->genre_id]);


        book_type_avail::where('book_id', $bookId)
            ->where('type', $request->type) 
            ->update([
                'availability' => $request->status,
            ]);

        // Log to admin history update changes
        DB::table('admin_history')->insert([
            'admin_id'       => Auth::id(),
            'book_id'        => $bookId,
            'description'    => "Updated book: " . $request->title,
            'change_created' => now()
        ]);

        return redirect()->route('admin.manageBooks')->with('success', 'Book updated successfully!');
    }

    // Remove book
    public function destroyBook($bookId, $type)
    {
        if (Auth::user()->role !== 'librarian') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }
    
       
        $book = Book::findOrFail($bookId);
    
       
        $isBorrowed = history::where('book_id', $bookId)
            ->where('type', $type) 
            ->whereIn('status', ['borrowed', 'due'])
            ->exists();
    
        if ($isBorrowed) {
            return redirect()->back()->with('error', "Cannot delete the {$type} version. It is currently borrowed.");
        }
    
        
        book_type_avail::where('book_id', $bookId)
            ->where('type', $type)
            ->delete();
    
         // Log to admin history delete changes
        admin_history::create([
            'admin_id'       => Auth::id(),
            'book_id'        => $bookId,
            'book_title'     => $book->title, 
            'description'    => "Deleted Copy: {$book->title} ({$type})",
            'change_created' => now()
        ]);
    
        $remainingCopies = book_type_avail::where('book_id', $bookId)->count();
        // If a physical and e_book type doesn't exists, remove book entirely
        if ($remainingCopies === 0) {
            
            $book->authors()->detach();
            $book->genres()->detach();
            if ($book->image) {
               
                $imagePath = public_path('books/' . $book->image);
    
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
          
            admin_history::create([
                'admin_id'       => Auth::id(),
                'book_id'        => $bookId,
                'book_title'     => $book->title,
                'description'    => "All copies removed. Permanently deleted parent book: {$book->title}",
                'change_created' => now()
            ]);
    
            $book->delete(); 
    
            return redirect()->route('admin.manageBooks')->with('success', "{$type} copy deleted. No copies left, so the book was removed entirely.");
        }
    
        return redirect()->route('admin.manageBooks')->with('success', "{$type} copy deleted successfully. Other versions remain.");
    }

    public function manageRecords()
    {
        $books = history::with(['book', 'user'])
            ->where('type', 'physical')
            ->orderBy('date_borrowed', 'desc')
            ->paginate(10);

        return view('dashboard.librarian.manage_records', compact('books'));
    }

    // Update book status
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
}
