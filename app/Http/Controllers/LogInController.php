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

        $user = user_account::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = user_account::find(Auth::id());
            if ($user) {
                $user->last_active = now();
                $user->save();
            }
        }

        Auth::logout();
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'Logged out successfully!');
    }
}
