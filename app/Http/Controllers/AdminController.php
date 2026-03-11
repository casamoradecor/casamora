<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Categoria;
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
     * Dashboard Principal.
     */
    public function index()
    {
        if (Auth::id() !== 1) return redirect('/');
        return view('admin');
    }

    /**
     * LISTAGEM DE PRODUTOS.
     */
    public function createProduto()
    {
        if (Auth::id() !== 1) return redirect('/');

        // Carregamos a categoria junto com o produto para mostrar o nome na tabela
        $produtos = Produto::with('categoria')->get();
        return view('admin.create', compact('produtos'));
    }

    /**
     * FORMULÁRIO NOVO: Agora envia as categorias para o formulário.
     */
    public function novoProduto()
    {
        if (Auth::id() !== 1) return redirect('/');

        $categorias = Categoria::where('status', 'ativa')->get();

        return view('admin.novo', compact('categorias'));
    }

    /**
     * SALVAR NOVO PRODUTO.
     */
    public function storeProduto(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'preco' => 'required',
            'estoque' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem' => 'required|image'
        ]);

        $path = $request->file('imagem')->store('produtos', 'public');

        Produto::create([
            'nome' => $request->nome,
            'preco' => $request->preco,
            'estoque' => $request->estoque,
            'categoria_id' => $request->categoria_id,
            'imagem' => $path,
            'lancamento' => $request->has('lancamento'),
            'descricao' => $request->descricao
        ]);

        return redirect()->route('admin.produtos.create')->with('sucesso', 'PRODUTO CADASTRADO!');
    }

    /**
     * FORMULÁRIO EDITAR: Também envia as categorias para permitir troca.
     */
    public function editProduto($id)
    {
        if (Auth::id() !== 1) return redirect('/');

        $produto = Produto::findOrFail($id);
        $categorias = Categoria::where('status', 'ativa')->get();

        return view('admin.editar', compact('produto', 'categorias'));
    }

    /**
     * ATUALIZAR PRODUTO.
     */
    public function updateProduto(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        // Adicionamos 'descricao' na lista de campos permitidos
        $produto->update($request->only(['nome', 'preco', 'estoque', 'categoria_id', 'descricao']));

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

        $produtos = Produto::where('lancamento', true)->latest()->get();
        $categorias = Categoria::where('status', 'ativa')->get();

        return view('admin.visual', compact('produtos', 'categorias'));
    }

    /**
     * EXCLUIR PRODUTO (SoftDelete).
     */
    public function destroyProduto($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();
        return redirect()->back()->with('sucesso', 'PRODUTO REMOVIDO!');
    }

    /**
     * TOGGLE LANÇAMENTO.
     */
    public function toggleLancamento($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->lancamento = !$produto->lancamento;
        $produto->save();

        return redirect()->back()->with('sucesso', 'STATUS DE LANÇAMENTO ATUALIZADO!');
    }

    /* --- PERSONALIZAÇÃO DA HOME (IMAGENS ESTÁTICAS) --- */

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
