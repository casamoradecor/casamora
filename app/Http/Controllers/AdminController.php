<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\ShoppablePoint;
use Illuminate\Support\Facades\Auth;
use App\Models\HomeSlot;
use Illuminate\Support\Facades\Storage;

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

        $produtos = Produto::with('categoria')->get();
        return view('admin.create', compact('produtos'));
    }

    /**
     * FORMULÁRIO NOVO.
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
            'codigo' => 'required|unique:produtos,codigo|max:50', // Validação do SKU
            'nome' => 'required',
            'preco' => 'required',
            'estoque' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem' => 'required|image',
            'peso' => 'required|numeric',
            'largura' => 'required|integer',
            'altura' => 'required|integer',
            'comprimento' => 'required|integer',
        ]);

        $path = $request->file('imagem')->store('produtos', 'public');

        Produto::create([
            'codigo' => $request->codigo, // Salvando o SKU
            'nome' => $request->nome,
            'preco' => $request->preco,
            'estoque' => $request->estoque,
            'categoria_id' => $request->categoria_id,
            'imagem' => $path,
            'lancamento' => $request->has('lancamento'), // Captura o Switch
            'descricao' => $request->descricao,
            'peso' => $request->peso,
            'largura' => $request->largura,
            'altura' => $request->altura,
            'comprimento' => $request->comprimento,
        ]);

        return redirect()->route('admin.produtos.create')->with('sucesso', 'PRODUTO CADASTRADO!');
    }

    /**
     * FORMULÁRIO EDITAR.
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

        $request->validate([
            'codigo' => 'required|max:50|unique:produtos,codigo,' . $id, // Único, ignorando o próprio ID
            'nome' => 'required',
            'preco' => 'required',
            'estoque' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'peso' => 'required|numeric',
            'largura' => 'required|integer',
            'altura' => 'required|integer',
            'comprimento' => 'required|integer',
        ]);

        $produto->update($request->only(['codigo', 'nome', 'preco', 'estoque', 'categoria_id', 'descricao', 'peso', 'largura', 'altura', 'comprimento']));

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
        $produtos = Produto::all();
        $categorias = Categoria::all();
        $shoppablePoints = ShoppablePoint::with('produto')->get();
        $slots = HomeSlot::all()->keyBy('slot_number');

        return view('admin.visual', compact('produtos', 'categorias', 'shoppablePoints', 'slots'));
    }

    public function saveHotspot(Request $request)
    {
        if (Auth::id() !== 1) return response()->json(['error' => 'Unauthorized'], 403);

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'x_pos' => 'required|numeric',
            'y_pos' => 'required|numeric',
        ]);

        ShoppablePoint::create([
            'produto_id' => $request->produto_id,
            'x_pos' => $request->x_pos,
            'y_pos' => $request->y_pos,
        ]);

        return redirect()->back()->with('sucesso', 'PONTO VINCULADO AO AMBIENTE!');
    }

    /**
     * 4. EXCLUIR PONTO (Caso o admin erre o lugar)
     */
    public function deleteHotspot($id)
    {
        if (\Illuminate\Support\Facades\Auth::id() !== 1) return redirect('/');

        $ponto = \App\Models\ShoppablePoint::findOrFail($id);
        $ponto->delete();

        return redirect()->back()->with('sucesso', 'PONTO REMOVIDO COM SUCESSO!');
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

    public function toggleLancamento($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->lancamento = !$produto->lancamento;
        $produto->save();
        return redirect()->back()->with('sucesso', 'STATUS DE LANÇAMENTO ATUALIZADO!');
    }

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
        // Tornamos os campos opcionais na validação para um não travar o outro
        $request->validate([
            'cat_img' => 'nullable|image',
            'categoria_id' => 'nullable|exists:categorias,id'
        ]);

        // 1. Se subiu foto, salva a foto
        if ($request->hasFile('cat_img')) {
            $request->file('cat_img')->move(public_path('assets'), "categoria_{$id}.png");
        }

        // 2. Se selecionou categoria, salva o vínculo no banco
        if ($request->filled('categoria_id')) {
            \App\Models\HomeSlot::updateOrCreate(
                ['slot_number' => $id],
                ['categoria_id' => $request->categoria_id]
            );
        }

        return redirect()->back()->with('sucesso', 'ALTERAÇÕES SALVAS!');
    }
}
