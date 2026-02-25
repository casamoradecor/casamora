<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    // Lista os produtos no painel admin
    public function index()
    {
        $produtos = Produto::with('categoria')->get();
        return view('admin.produtos.index', compact('produtos'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.produtos.create', compact('categorias'));
    }

    // Salva o produto real no banco
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'preco' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $dados = $request->all();

        // Lógica simples de Upload de Imagem
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $caminho = $request->imagem->store('produtos', 'public');
            $dados['imagem'] = $caminho; // Salva o caminho no banco
        }

        Produto::create($dados);

        return redirect()->route('admin.produtos.index')->with('success', 'Item de decoração cadastrado!');
    }
}