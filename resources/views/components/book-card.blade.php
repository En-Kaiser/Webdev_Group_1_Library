@props(['title', 'cover' => null, 'author' => null, 'id' => null, 'genre' => 'Uncategorized'])

<div class="book-item">
  <a href="{{ $id ? route('books.show', $id) : '#' }}" class="text-decoration-none">
    <div class="book-placeholder shadow-sm">
      @if($cover)
      <img src="{{ asset('storage/books' . $cover) }}" alt="{{ $title }}">
      @else
      <i class="bi bi-book text-white-50" style="font-size: 2rem;"></i>
      @endif
    </div>

    <h5 class="book-title mt-3">{{ $title }}</h5>

    @if($author)
    <p class="text-muted small">{{ $author }}</p>
    @endif
  </a>
</div>