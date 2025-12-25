<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Testing extends Controller
{
    public function index()
    {
        return view('testing');
    }

    public function show($id)
    
    {
        return view('testing_id', ['id' => $id]);
    }
}
