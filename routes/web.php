<?php

use App\Http\Controllers\Testing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/testing',[Testing::class, 'index']);

Route::get('/testing/{id}',[Testing::class, 'show']);

Route::fallback(function(){
    return redirect() -> route('welcome');
});
 