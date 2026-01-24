<?php

namespace App\Http\Controllers;

use App\Models\admin_history;
use App\Models\author;
use App\Models\genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorGenreController extends Controller
{
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
}
