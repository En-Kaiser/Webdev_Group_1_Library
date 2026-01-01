<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="container py-4">

    <h2>Dashboard</h2>

    <form method="GET" action="{{ route('dashboard.search') }}" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" placeholder="Search" value="{{ $searchTerm ?? '' }}">
            <button type="submit">Search</button>
        </div>
    </form>
</body>

</html>