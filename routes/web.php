<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarrinhoController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
    return view('login'); // Aponta para o login.blade.php
}); 

Route::prefix('admin')->group(function () {
    Route::resource('produtos', ProdutoController::class);
});

Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::get('/carrinho/listar', [CarrinhoController::class, 'listar'])->name('carrinho.listar');
Route::post('/carrinho/diminuir', [CarrinhoController::class, 'diminuir'])->name('carrinho.diminuir');
Route::get('/checkout', [CarrinhoController::class, 'checkout'])->name('checkout');