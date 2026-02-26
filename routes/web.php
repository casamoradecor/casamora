<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PerfilController; 

/*
|--------------------------------------------------------------------------
| HOME & PÁGINAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| CARRINHO DE COMPRAS (Sessão & API)
|--------------------------------------------------------------------------
*/
Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::get('/carrinho/listar', [CarrinhoController::class, 'listar'])->name('carrinho.listar');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::post('/carrinho/diminuir', [CarrinhoController::class, 'diminuir'])->name('carrinho.diminuir');


/*
|--------------------------------------------------------------------------
| FLUXO DE CHECKOUT E FINALIZAÇÃO (Requer Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Tela de fechamento
    Route::get('/checkout', [CarrinhoController::class, 'checkout'])->name('checkout');
    
    // Processamento do pedido no banco
    Route::post('/finalizar-pedido', [CarrinhoController::class, 'finalizarPedido'])->name('pedido.finalizar');
    
    // Tela de sucesso (após salvar no banco)
    Route::get('/pedido-sucesso/{id}', function($id) {
        return view('pedidos.sucesso', ['pedidoId' => $id]);
    })->name('pedido.sucesso');
});


/*
|--------------------------------------------------------------------------
| DASHBOARD & HISTÓRICO DO CLIENTE (Minha Conta)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Painel principal (Resumo)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Lista de todos os pedidos do cliente
    Route::get('/meus-pedidos', function () {
        $pedidos = Auth::user()->pedidos()->orderBy('created_at', 'desc')->get();
        return view('pedidos.index', compact('pedidos'));
    })->name('pedidos.index');

    // Detalhes de um pedido específico
    Route::get('/meus-pedidos/{id}', function ($id) {
        $pedido = Auth::user()->pedidos()->with('itens.produto')->findOrFail($id);
        return view('pedidos.show', compact('pedido'));
    })->name('pedidos.show');

    // --- ROTAS DE PERFIL (Movi para dentro do grupo auth) ---
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    
    // A ROTA QUE ESTAVA FALTANDO AQUI:
    Route::put('/perfil/senha', [PerfilController::class, 'updatePassword'])->name('perfil.password.update');
});


/*
|--------------------------------------------------------------------------
| PAINEL ADMINISTRATIVO (Gestão da Loja)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('produtos', ProdutoController::class);
});


/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO (Laravel Breeze/Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';