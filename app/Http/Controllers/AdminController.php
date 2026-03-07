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
            return redirect()->route('admin.produtos.create');
        }
        return redirect('/')->with('erro', 'acesso negado');
    }

    /**
     * Dashboard Principal (Redireciona ou mostra resumo).
     */
    public function index()
{
    if (Auth::id() !== 1) return redirect('/');
    
    // Retorna a view da Home (banners, destaques, etc)
    return view('admin'); 
}

    /**
     * LISTAGEM: Exibe a tabela estilo Nuvemshop (resources/views/admin/create.blade.php).
     */
    public function createProduto()
{
    if (Auth::id() !== 1) return redirect('/');
    
    $produtos = Produto::all(); 
    return view('admin.create', compact('produtos')); 
}

    /**
     * FORMULÁRIO NOVO: Abre a tela de cadastro (resources/views/admin/novo.blade.php).
     */
    public function novoProduto()
    {
        if (Auth::id() !== 1) return redirect('/');
        return view('admin.novo'); 
    }

    /**
     * SALVAR NOVO: Processa o cadastro no banco.
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
            'imagem' => $path,
            'lancamento' => $request->has('lancamento')
        ]);

        return redirect()->route('admin.produtos.create')->with('sucesso', 'PRODUTO CADASTRADO!');
    }

    /**
     * FORMULÁRIO EDITAR: Abre a tela de edição (resources/views/admin/editar.blade.php).
     */
    public function editProduto($id)
    {
        if (Auth::id() !== 1) return redirect('/');
        $produto = Produto::findOrFail($id);
        return view('admin.editar', compact('produto'));
    }

    /**
     * ATUALIZAR: Processa a edição dos dados.
     */
    public function updateProduto(Request $request, $id) {
        $produto = Produto::findOrFail($id);
        
        $produto->update($request->only(['nome', 'preco', 'estoque', 'categoria_id']));

        // Atualiza lançamento separadamente (checkbox)
        $produto->lancamento = $request->has('lancamento');
        
        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('produtos', 'public');
            $produto->imagem = $path;
        }

        $produto->save();

        return redirect()->route('admin.produtos.create')->with('sucesso', 'PRODUTO ATUALIZADO!');
    }
    public function visualEditor()
{
    if (Auth::id() !== 1) return redirect('/');

    // Busca os dados exatos que a Home usa
    $produtos = Produto::where('lancamento', true)->latest()->get();
    $categorias = \App\Models\Categoria::where('status', 'ativa')->get();

    return view('admin.visual', compact('produtos', 'categorias'));
}

    /**
     * EXCLUIR: Remove o produto do banco.
     */
    public function destroyProduto($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();
        return redirect()->back()->with('sucesso', 'PRODUTO REMOVIDO!');
    }

    /**
     * TOGGLE LANÇAMENTO: Alterna o destaque no carrossel.
     */
    public function toggleLancamento($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->lancamento = !$produto->lancamento;
        $produto->save();

        return redirect()->back()->with('sucesso', 'STATUS DE LANÇAMENTO ATUALIZADO!');
    }

    /* --- PERSONALIZAÇÃO DA HOME --- */

    public function uploadBanner(Request $request) 
    {
        $request->validate(['hero_img' => 'required|image']);
        $request->file('hero_img')->move(public_path('assets'), 'hero_banner.png');
        return redirect()->back()->with('sucesso', 'BANNER ATUALIZADO!');
    }

    public function updateDestaque(Request $request) 
    {
        $request->validate(['destaque_img' => 'required|image']);
        $request->file('destaque_img')->move(public_path('assets'), 'destaque_home.png');
        return redirect()->back()->with('sucesso', 'DESTAQUE ATUALIZADO!');
    }

    public function updateShoppable(Request $request)
    {
        $request->validate(['shoppable_img' => 'required|image']);
        $request->file('shoppable_img')->move(public_path('assets'), 'shoppable_main.png');
        return redirect()->back()->with('sucesso', 'AMBIENTE ATUALIZADO!');
    }

    public function updateCategoria(Request $request, $id) 
    {
        $request->validate(['cat_img' => 'required|image']);
        $request->file('cat_img')->move(public_path('assets'), "categoria_{$id}.png");
        return redirect()->back()->with('sucesso', 'CATEGORIA ATUALIZADA!');
    }
}