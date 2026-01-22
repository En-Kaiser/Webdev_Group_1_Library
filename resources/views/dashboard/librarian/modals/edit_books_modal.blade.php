<!-- EDIT BOOK MODAL -->
<div class="modal fade" id="editBookModal{{ $book->book_id }}" tabindex="-1" aria-labelledby="editBookModalLabel{{ $book->book_id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-form-section">
            <div class="modal-form">
                <h2>EDIT BOOK</h2>

                <form action="{{ route('librarian.books.update', $book->book_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <input type="text" name="title" value="{{ $book->title }}" class="form-control mb-2" placeholder="Title" required>

                    <!-- Description -->
                    <textarea name="short_description" class="form-control mb-2" placeholder="Description">{{ $book->short_description }}</textarea>

                    <!-- Year -->
                    <input type="number" name="year" value="{{ $book->year }}" class="form-control mb-2" placeholder="Year">

                    <!-- Author dropdown -->
                    <select name="author_id" class="form-select mb-2">
                        @foreach($authors as $author)
                            <option value="{{ $author->author_id }}" 
                                {{ $book->author_id == $author->author_id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Genre dropdown -->
                    <select name="genre_id" class="form-select mb-2">
                        @foreach($genres as $genre)
                            <option value="{{ $genre->genre_id }}" 
                                {{ $book->genre_id == $genre->genre_id ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Type -->
                    <select name="type" class="form-select mb-2">
                        <option value="physical " {{ $book->type == 'Physical Book' ? 'selected' : '' }}>Physical Book</option>
                        <option value="e_book" {{ $book->type == 'E-Book' ? 'selected' : '' }}>E-Book</option>
                    </select>

                    <!-- Status -->
                    <select name="status" class="form-select mb-3">
                        <option value="Available" {{ $book->availability == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Borrowed" {{ $book->availability == 'Borrowed' ? 'selected' : '' }}>Borrowed</option>
                    </select>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                        <button type="submit" class="btn btn-add-book">UPDATE BOOK</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
