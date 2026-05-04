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
        $produtos = Produto::with('categoria')->latest()->get();
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
            'preco' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'imagem' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'peso' => 'required|numeric',
            'largura' => 'required|integer',
            'altura' => 'required|integer',
            'comprimento' => 'required|integer',
            'estoque' => 'required|integer',
            'descricao' => 'nullable'
        ]);

        $dados = $request->all();

        $preco = str_replace(',', '.', $request->preco);
        $dados['preco'] = (float) $preco;

        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $dados['imagem'] = $request->imagem->store('produtos', 'public');
        }

        Produto::create($dados);
        return redirect()->route('admin.produtos.create')->with('success', 'ITEM CADASTRADO!');
    }

    public function edit($id)
    {
        $produto = Produto::findOrFail($id);
        $categorias = Categoria::all();
        return view('admin.editar', compact('produto', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $request->validate([
            'codigo' => 'required|max:50|unique:produtos,codigo,' . $id,
            'nome' => 'required|max:255',
            'preco' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
            'estoque' => 'required|integer',
        ]);

        $dados = $request->all();
        $preco = str_replace(',', '.', $request->preco);
        $dados['preco'] = (float) $preco;

        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem')) {
            if ($produto->imagem) Storage::disk('public')->delete($produto->imagem);
            $dados['imagem'] = $request->imagem->store('produtos', 'public');
        }

        $produto->update($dados);
        return redirect()->route('admin.produtos.create')->with('success', 'ITEM ATUALIZADO!');
    }

    public function vitrine(Request $request)
    {
        $categorias = Categoria::all();
        $query = Produto::with('categoria');

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'LIKE', '%' . $busca . '%')
                    ->orWhereHas('categoria', function ($qCategoria) use ($busca) {
                        $qCategoria->where('nome', 'LIKE', '%' . $busca . '%');
                    });
            });
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
    public function apiBusca(Request $request)
    {
        $termo = $request->query('q');

        if (strlen($termo) < 3) {
            return response()->json([]);
        }

        $produtos = \App\Models\Produto::with('categoria')
            ->where(function ($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                    ->orWhereHas('categoria', function ($qCategoria) use ($termo) {
                        $qCategoria->where('nome', 'LIKE', "%{$termo}%");
                    });
            })
            ->limit(6)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'nome' => $p->nome,
                    'preco' => number_format($p->preco, 2, ',', '.'),
                    'link' => route('produto.show', $p->id),
                    'imagem' => $p->imagem ? \Storage::url($p->imagem) : asset('assets/vasomora.png')
                ];
            });

        return response()->json($produtos);
    }
}
