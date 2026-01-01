<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showSignUp()
    {
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $first_name = request('first_name');
        $last_name = request('last_name');
        $course = request('course');
        $email = request('email');
        $password = request('password');

        DB::table('user_accounts')->insert([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'course' => $course,
            'email' => $email,
            'password' => bcrypt($password),
            'date_joined' => now(),
        ]);

        return redirect('/login');
    }

    public function showLogIn()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = request('email');
        $password = request('password');

        $user = DB::table('user_accounts')->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            return redirect('/home');
        }
    }

    // logout function to be implemented
}
