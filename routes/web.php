<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaController;

/*
|--------------------------------------------------------------------------
| PAINEL ADMINISTRATIVO (AdminController)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Acesso e Dashboard Principal
    Route::get('/acessar', [AdminController::class, 'validarAcesso'])->name('admin.access');
    Route::get('/editar', [AdminController::class, 'index'])->name('admin.index');

    // PRODUTOS
    Route::get('/visualizar', [AdminController::class, 'createProduto'])->name('admin.produtos.create');
    Route::get('/produtos/novo', [AdminController::class, 'novoProduto'])->name('admin.produtos.novo');
    Route::get('/produtos/{id}/editar', [AdminController::class, 'editProduto'])->name('admin.produtos.edit');

    // AÇÕES
    Route::post('/produto', [AdminController::class, 'storeProduto'])->name('admin.produto.store');
    Route::put('/produto/{id}', [AdminController::class, 'updateProduto'])->name('admin.produto.update');
    Route::delete('/produto/{id}', [AdminController::class, 'destroyProduto'])->name('admin.produto.destroy');
    Route::post('/produtos/{id}/toggle-lancamento', [AdminController::class, 'toggleLancamento'])->name('admin.produto.toggle-lancamento');

    // EDIÇÃO VISUAL DA HOME
    Route::get('/visual-da-loja', [AdminController::class, 'visualEditor'])->name('admin.visual.edit');
    Route::post('/banner', [AdminController::class, 'uploadBanner'])->name('admin.uploadBanner');
    Route::post('/destaque', [AdminController::class, 'updateDestaque'])->name('admin.updateDestaque');
    Route::post('/shoppable', [AdminController::class, 'updateShoppable'])->name('admin.updateShoppable');
    Route::post('/categoria/{id}', [AdminController::class, 'updateCategoria'])->name('admin.updateCategoria');
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('admin.categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');
});

/*
|--------------------------------------------------------------------------
| HOME & PÁGINAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produtos', [HomeController::class, 'shop'])->name('produtos.index');


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
