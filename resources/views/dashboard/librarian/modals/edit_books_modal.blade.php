<!-- EDIT BOOK MODAL -->
<div class="modal fade" id="editBookModal{{ $book->book_id }}" tabindex="-1" aria-labelledby="editBookModalLabel{{ $book->book_id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-form-section">
            <div class="modal-form">
                <h2>EDIT BOOK</h2>

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('librarian.books.update', $book->book_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title + Cover -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" name="title" value="{{ $book->title }}" placeholder="Title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="text" id="editCoverImageText{{ $book->book_id }}" class="form-control" placeholder="Choose Book Cover" readonly>
                                    <input type="file" id="editCoverImageFile{{ $book->book_id }}" name="cover_image" class="d-none" accept="image/*" onchange="document.getElementById('editCoverImageText{{ $book->book_id }}').value = this.files[0]?.name || ''">
                                </div>
                                <button type="button" class="btn btn-light" onclick="document.getElementById('editCoverImageFile{{ $book->book_id }}').click()">
                                    Browse
                                </button>
                            </div>
                            @if($book->image)
                            <small class="text-muted d-block mt-1">Current: {{ $book->image }}</small>
                            @endif
                        </div>
                    </div>

                    <!-- Description + Year -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <textarea name="short_description" class="form-control" rows="2" placeholder="Description">{{ $book->short_description }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="year" value="{{ $book->year }}" class="form-control" placeholder="Year" min="1000" max="{{ date('Y') }}">
                        </div>
                    </div>

                    <!-- Author + Add Author -->
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <select name="author_id" class="form-select" required>
                                <option value="">Choose Author</option>
                                @foreach($authors as $author)
                                <option value="{{ $author->author_id }}" {{ $book->author_id == $author->author_id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-add-action" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                                + Add Author
                            </button>
                        </div>
                    </div>

                    <!-- Genre + Add Genre -->
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <select name="genre_id" class="form-select" required>
                                <option value="">Choose Genre</option>
                                @foreach($genres as $genre)
                                <option value="{{ $genre->genre_id }}" {{ $book->genre_id == $genre->genre_id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-add-action" data-bs-toggle="modal" data-bs-target="#addGenreModal">
                                + Add Genre
                            </button>
                        </div>
                    </div>

                    <!-- Type + Status -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <select name="type" class="form-select" required>
                                <option value="">Choose Type of Book</option>
                                <option value="physical" {{ $book->type == 'physical' ? 'selected' : '' }}>Physical Book</option>
                                <option value="e_book" {{ $book->type == 'e_book' ? 'selected' : '' }}>E-Book</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="status" class="form-select" required>
                                <option value="">Status</option>
                                <option value="available" {{ $book->availability == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="unavailable" {{ $book->availability == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                        <button type="submit" class="btn btn-add-book">UPDATE BOOK</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>