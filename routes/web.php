<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FreteController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\Admin\VendaController;
use App\Http\Controllers\Admin\SobreNosController;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| HOME & PÁGINAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produtos', [HomeController::class, 'shop'])->name('produtos.index');
Route::get('/produto/{id}', [ProdutoController::class, 'show'])->name('produto.show');
Route::get('/api/busca-produtos', [ProdutoController::class, 'apiBusca'])->name('api.produtos.busca');
Route::get('/sobre-nos', [App\Http\Controllers\Admin\SobreNosController::class, 'show'])->name('sobre.nos');
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| CARRINHO DE COMPRAS & WEBHOOK
|--------------------------------------------------------------------------
*/
Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::get('/carrinho/listar', [CarrinhoController::class, 'listar'])->name('carrinho.listar');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::post('/carrinho/diminuir', [CarrinhoController::class, 'diminuir'])->name('carrinho.diminuir');
Route::post('/carrinho/atualizar', [CarrinhoController::class, 'atualizarQtd'])->name('carrinho.atualizar');

// Retorno do Mercado Pago e Notificações (Webhook)
Route::get('/pedido/sucesso/{id}', [CarrinhoController::class, 'pedidoSucesso'])->name('pedido.sucesso');
Route::post('/webhook/mercadopago', [WebhookController::class, 'receberNotificacao']);

/*
|--------------------------------------------------------------------------
| FRETE
|--------------------------------------------------------------------------
*/
Route::get('/frete/calcular', [FreteController::class, 'calcular'])->name('frete.calcular');
Route::get('/frete/calcular-carrinho', [FreteController::class, 'calcularCarrinho'])->name('frete.calcular-carrinho');

/*
|--------------------------------------------------------------------------
| FLUXO DE CHECKOUT E FINALIZAÇÃO (Requer Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{id?}', [CarrinhoController::class, 'checkout'])->name('checkout');
    Route::post('/finalizar-pedido', [CarrinhoController::class, 'finalizarPedido'])->name('pedido.finalizar');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD & HISTÓRICO DO CLIENTE (Minha Conta)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Novas rotas agrupadas com os Controllers específicos
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/meus-pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/meus-pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');

    // Gestão de Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/senha', [PerfilController::class, 'updatePassword'])->name('perfil.password.update');
});
// Rota para a vitrine pública (onde estão os filtros)
Route::get('/produtos', [ProdutoController::class, 'vitrine'])->name('produtos.index');

/*
|--------------------------------------------------------------------------
| PAINEL ADMINISTRATIVO (AdminController)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Acesso e Dashboard Principal
    Route::get('/acessar', [AdminController::class, 'validarAcesso'])->name('admin.access');
    Route::get('/editar', [AdminController::class, 'index'])->name('admin.index');

    // PRODUTOS (Rotas de Resource e Customizadas)
    Route::get('/produtos/novo', [ProdutoController::class, 'create'])->name('admin.produtos.novo');
    Route::get('/produtos/{id}/editar', [ProdutoController::class, 'edit'])->name('admin.produtos.edit');
    Route::get('/visualizar', [AdminController::class, 'createProduto'])->name('admin.produtos.create');
    Route::resource('produtos', ProdutoController::class);
    Route::post('/produto', [ProdutoController::class, 'store'])->name('admin.produto.store');
    Route::put('/produto/{id}', [ProdutoController::class, 'update'])->name('admin.produto.update');
    Route::delete('/produto/{id}', [AdminController::class, 'destroyProduto'])->name('admin.produto.destroy');
    Route::post('/produtos/{id}/toggle-lancamento', [AdminController::class, 'toggleLancamento'])->name('admin.produto.toggle-lancamento');

    // CATEGORIAS
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('admin.categorias.index');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');

    // EDIÇÃO VISUAL DA HOME
    Route::get('/visual-da-loja', [AdminController::class, 'visualEditor'])->name('admin.visual.edit');
    Route::post('/banner', [AdminController::class, 'uploadBanner'])->name('admin.uploadBanner');
    Route::post('/destaque', [AdminController::class, 'updateDestaque'])->name('admin.updateDestaque');
    Route::post('/shoppable', [AdminController::class, 'updateShoppable'])->name('admin.updateShoppable');
    Route::post('/categoria/{id}', [AdminController::class, 'updateCategoria'])->name('admin.updateCategoria');

    Route::post('/admin/shoppable/save', [AdminController::class, 'saveHotspot'])->name('admin.saveHotspot');
    Route::delete('/admin/shoppable/delete/{id}', [App\Http\Controllers\AdminController::class, 'deleteHotspot'])->name('admin.deleteHotspot');
    Route::get('/sobre-nos/editar', [SobreNosController::class, 'edit'])->name('admin.sobre.edit');
    Route::post('/sobre-nos/texto', [SobreNosController::class, 'updateTexto'])->name('admin.sobre.updateTexto');
    Route::post('/sobre-nos/foto/{id}', [SobreNosController::class, 'updateFoto'])->name('admin.sobre.updateFoto');

    Route::post('/home/update-hero-text', [AdminController::class, 'updateHeroText'])->name('admin.updateHeroText');

    // Gestão de Vendas (Pedidos)
    Route::get('/vendas', [App\Http\Controllers\Admin\VendaController::class, 'index'])->name('admin.vendas.index');
    Route::get('/vendas/{id}', [App\Http\Controllers\Admin\VendaController::class, 'show'])->name('admin.pedidos.show');
    Route::post('/vendas/{id}/enviar', [App\Http\Controllers\Admin\VendaController::class, 'marcarComoEnviado'])->name('admin.pedidos.enviar');
    Route::post('/vendas/{id}/etiqueta', [VendaController::class, 'emitirEtiqueta'])->name('admin.pedidos.etiqueta');

    Route::get('/newsletter', [App\Http\Controllers\NewsletterController::class, 'adminIndex'])->name('admin.newsletter.index');
    Route::post('/newsletter/enviar', [App\Http\Controllers\NewsletterController::class, 'enviarEmail'])->name('admin.newsletter.enviar');
    Route::delete('/newsletter/{id}', [App\Http\Controllers\NewsletterController::class, 'destroy'])->name('admin.newsletter.destroy');
    Route::post('/newsletter/gerar-ia', [App\Http\Controllers\NewsletterController::class, 'gerarComIA'])
        ->name('admin.newsletter.gerar-ia');
});

/*
|--------------------------------------------------------------------------
| ENDEREÇOS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/meus-enderecos', [EnderecoController::class, 'index'])->name('enderecos.index');
    Route::get('/meus-enderecos/novo', [EnderecoController::class, 'create'])->name('enderecos.create');
    Route::post('/meus-enderecos', [EnderecoController::class, 'store'])->name('enderecos.store');
    Route::get('/meus-enderecos/{id}/editar', [EnderecoController::class, 'edit'])->name('enderecos.edit');
    Route::put('/meus-enderecos/{id}', [EnderecoController::class, 'update'])->name('enderecos.update');
    Route::delete('/meus-enderecos/{id}', [EnderecoController::class, 'destroy'])->name('enderecos.destroy');
});
/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
