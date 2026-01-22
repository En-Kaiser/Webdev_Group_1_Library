<!-- ADD GENRE MODAL -->
<div class="modal fade" id="addGenreModal" tabindex="-1" aria-labelledby="addGenreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-form-section">
            <div class="modal-form">
                <h2>ADD GENRE</h2>

                <form id="addGenreForm" method="POST" action="{{ route('librarian.genres.store') }}">
                    @csrf
                    <input type="text" name="name" id="genre_name" class="form-control" placeholder="Genre" required>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                        <button type="submit" class="btn btn-add-book">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
