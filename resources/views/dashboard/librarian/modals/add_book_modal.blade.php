<!-- ADD BOOK MODAL -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-form-section">
            <div class="modal-form">
                <h2>ADD BOOK</h2>

                <form action="{{ route('librarian.books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title + Cover -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" name="title" placeholder="Title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="text" id="coverImageText" class="form-control" placeholder="Choose Book Cover" readonly>
                                    <input type="file" id="coverImageFile" name="cover_image" class="d-none" accept="image/*" onchange="document.getElementById('coverImageText').value = this.files[0]?.name || ''">
                                </div>
                                <button type="button" class="btn btn-light" onclick="document.getElementById('coverImageFile').click()">
                                    Browse
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Description + Year -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <textarea name="short_description" class="form-control" rows="2" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="year" class="form-control" placeholder="Year" min="1000" max="{{ date('Y') }}">
                        </div>
                    </div>

                    <!-- Author + Add Author -->
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <select name="author_id" class="form-select" required>
                                <option value="">Choose Author</option>
                                @foreach($authors as $author)
                                <option value="{{ $author->author_id }}">{{ $author->name }}</option>
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
                                <option value="{{ $genre->genre_id }}">{{ $genre->name }}</option>
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
                                <option value="physical">Physical Book</option>
                                <option value="e_book">E-Book</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="status" class="form-select" required>
                                <option value="">Status</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                        <button type="submit" class="btn btn-add-book">ADD BOOK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>