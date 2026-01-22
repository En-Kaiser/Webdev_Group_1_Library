<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\user_account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LogInController extends Controller
{
    public function showLogIn()
    {
        $courses = DB::table('courses')->get();
        return view('auth.login', compact('courses'));
    }

    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;

        // Student login
        $user = user_account::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        // Librarian login
        $admin = Admin::where('email', $email)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Auth::login($admin);
            $request->session()->regenerate();
            return redirect('/librarian/dash');
        }

        // Invalid credentials
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'Logged out successfully!');
    }
}
