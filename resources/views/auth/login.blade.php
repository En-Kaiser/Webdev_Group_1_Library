<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>

<body>
    <h2>Log In</h2><br><br>
    <form method="post" action="{{ route('auth.login') }}">
        @csrf
        <input type="email" placeholder="Email" name="email"><br><br>
        <input type="password" placeholder="Password" name="password"><br><br>
        <button type="submit">Log In</button>
    </form>
</body>

</html>