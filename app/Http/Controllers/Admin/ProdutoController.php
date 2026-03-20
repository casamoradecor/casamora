<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('categoria')->get();
        return view('admin.create', compact('produtos'));
    }

    public function show($id)
    {
        $produto = Produto::with('categoria')->findOrFail($id);
        return view('produtos.show', compact('produto'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.novo', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:produtos,codigo|max:50',
            'nome' => 'required|max:255',
            'preco' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem' => 'image|mimes:jpeg,png,jpg|max:2048',
            'peso' => 'required|numeric',
            'largura' => 'required|integer',
            'altura' => 'required|integer',
            'comprimento' => 'required|integer',
        ]);

        $dados = $request->all();
        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $dados['imagem'] = $request->imagem->store('produtos', 'public');
        }

        // SALVAMENTO REAL
        Produto::create($dados);

        return redirect()->route('admin.produtos.index')->with('success', 'ITEM CADASTRADO!');
    }

    // ADICIONE ESTE MÉTODO (Ele é quem leva o código para a tela de editar)
    public function edit($id)
    {
        $produto = Produto::findOrFail($id);
        $categorias = Categoria::all();
        return view('admin.editar', compact('produto', 'categorias'));
    }

    // ADICIONE ESTE MÉTODO (Ele salva as alterações do código)
    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $request->validate([
            'codigo' => 'required|max:50|unique:produtos,codigo,' . $id,
            'nome' => 'required|max:255',
            'preco' => 'required|numeric',
        ]);

        $dados = $request->all();
        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem')) {
            if ($produto->imagem) Storage::disk('public')->delete($produto->imagem);
            $dados['imagem'] = $request->imagem->store('produtos', 'public');
        }

        $produto->update($dados);

        return redirect()->route('admin.produtos.index')->with('success', 'ITEM ATUALIZADO!');
    }

    public function vitrine(Request $request)
    {
        $categorias = Categoria::all();
        $query = Produto::with('categoria');
        if ($request->filled('busca')) {
            $query->where('nome', 'LIKE', '%' . $request->busca . '%');
        }
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->ordem == 'preco_min') {
            $query->orderBy('preco', 'asc');
        } elseif ($request->ordem == 'preco_max') {
            $query->orderBy('preco', 'desc');
        } else {
            $query->latest();
        }

        $produtos = $query->get();
        return view('produtos.index', compact('produtos', 'categorias'));
    }
}
