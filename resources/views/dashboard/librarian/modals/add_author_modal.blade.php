<!-- ADD AUTHOR MODAL -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-form-section">
            <div class="modal-form">
                <h2>ADD AUTHOR</h2>

                <form id="addAuthorForm" method="POST" action="{{ route('librarian.authors.store') }}">
                    @csrf
                    <input type="text" name="name" id="author_name" class="form-control" placeholder="Author" required>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                        <button type="submit" class="btn btn-add-book">SAVE</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>