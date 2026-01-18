<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\user_account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SignUpController extends Controller
{
    public function showSignUp()
    {
        $courses = DB::table('courses')->get();
        return view('auth.signup', compact('courses'));
    }

    public function signup(SignUpRequest $request)
    {
        $user_account = new user_account();
        $user_account->first_name =  $request->input('first_name');
        $user_account->last_name = $request->input('last_name');
        $user_account->course = $request->input('course');
        $user_account->email = $request->input('email');
        $user_account->password = Hash::make($request->input('password'));
        $user_account->date_joined = NOW();
        $user_account->save();

        Auth::login($user_account);

        return redirect('/home');
    }

    public function json_string()
    {
        return user_account::all();
    }



    // logout function to be implemented
}
