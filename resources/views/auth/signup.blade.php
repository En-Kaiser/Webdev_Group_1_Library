<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>
    <h2>Sign Up</h2>
    <form method="post" action="{{  route('auth.signup') }}">
        @csrf
        <input type="text" placeholder="First Name" name="first_name"><br><br>
        <input type="text" placeholder="Last Name" name="last_name"><br><br>
        <select name="course" id="course">
            <option value="BSIT">BSIT</option>
            <option value="BSCS">BSCS</option>
        </select><br><br>
        <input type="email" placeholder="Email" name="email"><br><br>
        <input type="password" placeholder="Password" name="password"><br><br>
        <button type="submit">
            Create Account
        </button>
    </form>
</body>

</html>