<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProdutoController;

Route::get('/', function () {
    return view('index'); // Aponta para o index.blade.php
});

Route::get('/login', function () {
    return view('login'); // Aponta para o login.blade.php
}); 

Route::prefix('admin')->group(function () {
    Route::resource('produtos', ProdutoController::class);
});