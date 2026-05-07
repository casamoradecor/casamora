<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\ProdutoVisualizacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;

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

        $idsRecomendados = Cache::remember("ai_recomendacoes_v2_produto_{$id}", 86400, function () use ($produto, $id) {

            $catalogo = Produto::where('id', '!=', $produto->id)
                ->inRandomOrder()
                ->take(30)
                ->get(['id', 'nome']);

            $textoCatalogo = $catalogo->map(fn($p) => "ID:{$p->id} - {$p->nome}")->implode(", ");

            $prompt = "Você é um renomado Designer de Interiores e Curador de Estilo da 'Casa MORÁ'.\n\n"
                . "CONTEXTO: O cliente está interessado no produto '{$produto->nome}' da categoria '" . ($produto->categoria->nome) . "'.\n\n"
                . "SUA MISSÃO: Selecione exatamente 3 produtos da lista abaixo que melhor COMPLEMENTEM este item para criar um ambiente sofisticado e completo.\n\n"
                . "REGRAS CRUCAIS DE CURADORIA:\n"
                . "1. DIVERSIDADE DE CATEGORIAS: Evite sugerir produtos da mesma categoria '" . ($produto->categoria->nome) . "'. Priorize itens que o cliente usaria JUNTO com o atual (ex: se ele vê uma mesa, sugira um vaso, um tapete ou uma cadeira).\n"
                . "2. ESTILO E HARMONIA: Os itens escolhidos devem ter a mesma linguagem visual (material, cor e proposta de design) do produto principal.\n"
                . "3. LISTA DE CANDIDATOS: [{$textoCatalogo}]\n\n"
                . "SAÍDA OBRIGATÓRIA: Responda APENAS os 3 IDs numéricos separados por vírgula. Não escreva explicações, nem saudações. Exemplo: 7,15,22";

            try {
                $response = Http::timeout(8)->withToken(env('GROQ_API_KEY'))->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.1
                ]);

                if ($response->successful()) {
                    $texto = $response->json()['choices'][0]['message']['content'] ?? '';
                    preg_match_all('/\d+/', $texto, $matches);
                    $ids = array_slice($matches[0], 0, 3);

                    if (count($ids) === 3) return $ids;
                }
            } catch (\Exception $e) {
            }

            return Produto::where('categoria_id', $produto->categoria_id)
                ->where('id', '!=', $id)
                ->take(3)
                ->pluck('id')
                ->toArray();
        });

        $produtosRelacionados = collect();
        if (!empty($idsRecomendados)) {
            $ordemSql = implode(',', $idsRecomendados);
            $produtosRelacionados = Produto::whereIn('id', $idsRecomendados)
                ->orderByRaw("FIELD(id, {$ordemSql})")
                ->get();
        }

        return view('produtos.show', compact('produto', 'produtosRelacionados'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.novo', compact('categorias'));
    }

    public function store(ProdutoStoreRequest $request)
    {
        $dados = $request->validated();

        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $dados['imagem'] = $request->file('imagem')->store('produtos', 'public');
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

    public function update(ProdutoUpdateRequest $request, $id)
    {
        $produto = Produto::findOrFail($id);
        $dados = $request->validated();

        $dados['lancamento'] = $request->has('lancamento');

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {

            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $dados['imagem'] = $request->file('imagem')->store('produtos', 'public');
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
