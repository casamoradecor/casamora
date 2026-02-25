<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index'); // Aponta para o index.blade.php
});

Route::get('/login', function () {
    return view('login'); // Aponta para o login.blade.php
}); 