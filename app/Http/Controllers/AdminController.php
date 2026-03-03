<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Ponte de validação: verifica se é o ID 1 antes de liberar o acesso.
     */
    public function validarAcesso()
    {
        if (Auth::check() && Auth::id() === 1) {
            return redirect()->route('admin.index');
        }
        return redirect('/')->with('erro', 'acesso negado');
    }

    /**
     * Exibe a página principal do painel administrativo.
     */
    public function index()
    {
        if (Auth::id() !== 1) {
            return redirect('/');
        }
        $produtos = Produto::all();
        return view('admin', compact('produtos')); 
    }

    /**
     * Atualiza o Banner Principal (hero_banner.png).
     */
    public function uploadBanner(Request $request) 
    {
        $request->validate(['hero_img' => 'required|image']);
        $request->file('hero_img')->move(public_path('assets'), 'hero_banner.png');
        return redirect()->back()->with('sucesso', 'banner atualizado!');
    }
     /**
     * Atualiza o Banner Principal (hero_banner.png).
     */
    public function updateProduto(Request $request, $id) {
    $produto = Produto::findOrFail($id);
    
    // Atualiza nome, preço, estoque e categoria de uma vez
    $produto->update($request->only(['nome', 'preco', 'estoque', 'categoria_id']));

    if ($request->hasFile('imagem')) {
        $path = $request->file('imagem')->store('produtos', 'public');
        $produto->update(['imagem' => $path]);
    }

    return redirect()->back()->with('sucesso', 'produto e estoque atualizados!');
}
    /**
     * Atualiza a imagem de Destaque (destaque_home.png).
     */
    public function updateDestaque(Request $request) 
    {
        $request->validate(['destaque_img' => 'required|image']);
        $request->file('destaque_img')->move(public_path('assets'), 'destaque_home.png');
        return redirect()->back()->with('sucesso', 'imagem de destaque atualizada!');
    }

    /**
     * Atualiza a imagem do ambiente
     */
    public function updateShoppable(Request $request)
    {
        $request->validate(['shoppable_img' => 'required|image']);
        $request->file('shoppable_img')->move(public_path('assets'), 'shoppable_main.png');
        return redirect()->back()->with('sucesso', 'imagem do ambiente atualizada!');
    }

    /**
     * Atualiza as imagens das categorias (categoria_1.png, etc).
     */
    public function updateCategoria(Request $request, $id) 
    {
        $request->validate(['cat_img' => 'required|image']);
        $request->file('cat_img')->move(public_path('assets'), "categoria_{$id}.png");
        return redirect()->back()->with('sucesso', 'categoria atualizada!');
    }

    /**
     * Salva um novo produto no banco de dados.
     */
    public function storeProduto(Request $request)
    {
        $request->validate([
        'nome' => 'required',
        'preco' => 'required',
        'estoque' => 'required|integer',
        'categoria_id' => 'required',
        'imagem' => 'required|image'
    ]);

    $path = $request->file('imagem')->store('produtos', 'public');

    Produto::create([
        'nome' => $request->nome,
        'preco' => $request->preco,
        'estoque' => $request->estoque,
        'categoria_id' => $request->categoria_id,
        'imagem' => $path
    ]);

    return redirect()->back()->with('sucesso', 'produto cadastrado com estoque!');
}

    /**
     * Remove um produto do banco de dados.
     */
    public function destroyProduto($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();
        return redirect()->back()->with('sucesso', 'produto removido com sucesso!');
    }
}