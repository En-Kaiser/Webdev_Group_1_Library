<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- Borrow Book Form -->
    <form method="post" action="{{ route('books.borrow', $book->book_id) }}">
        @csrf
        <!-- forms -->
    </form>
</body>

</html>