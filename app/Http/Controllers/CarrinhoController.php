<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    /**
     * Retorna os dados atuais do carrinho para o JS (Sidebar)
     */
    public function listar()
    {
        $carrinho = session()->get('carrinho', []);
        return response()->json([
            'itens' => $carrinho,
            'total' => $this->calcularTotal($carrinho)
        ]);
    }

    /**
     * Adiciona um item ao carrinho via AJAX
     */
    public function adicionar(Request $request)
    {
        // 1. Validação rigorosa: se não veio ID, para tudo aqui.
        if (!$request->produto_id) {
            return response()->json(['success' => false, 'message' => 'ID ausente'], 400);
        }

        $produto = Produto::find($request->produto_id);

        // 2. Só prossegue se o produto REALMENTE existir no banco
        if (!$produto) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado'], 404);
        }

        $carrinho = session()->get('carrinho', []);

        // 3. Trata a URL da imagem (Garante que nunca seja null)
        $caminho = $produto->imagem;
        if ($caminho && str_contains($caminho, 'assets')) {
            $urlFinal = asset(ltrim($caminho, '/'));
        } else {
            $urlFinal = $caminho ? \Storage::url($caminho) : asset('assets/vasomora.png');
        }

        // 4. Grava na sessão APENAS se tivermos dados válidos
        if(isset($carrinho[$produto->id])) {
            $carrinho[$produto->id]['quantidade']++;
        } else {
            $carrinho[$produto->id] = [
                "id" => $produto->id,
                "nome" => $produto->nome,
                "quantidade" => 1,
                "preco" => $produto->preco,
                "imagem" => $urlFinal
            ];
        }

        session()->put('carrinho', $carrinho);

        return response()->json([
            'success' => true,
            'itens' => $carrinho,
            'total' => $this->calcularTotal($carrinho)
        ]);
    }

    /**
     * Diminui a quantidade ou remove o item do carrinho
     */
    public function diminuir(Request $request)
    {
        $carrinho = session()->get('carrinho', []);
        $id = $request->produto_id;

        if(isset($carrinho[$id])) {
            if($carrinho[$id]['quantidade'] > 1) {
                $carrinho[$id]['quantidade']--;
            } else {
                unset($carrinho[$id]);
            }
            session()->put('carrinho', $carrinho);
        }

        return response()->json([
            'success' => true,
            'itens' => $carrinho,
            'total' => $this->calcularTotal($carrinho)
        ]);
    }

    /**
     * Função auxiliar para calcular o valor total da sessão
     */
    private function calcularTotal($carrinho)
    {
        $total = 0;
        foreach($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }

    /**
     * Exibe a tela de Checkout (na pasta pedidos)
     */
    public function checkout()
    {
        $carrinho = session()->get('carrinho', []);

        if(empty($carrinho)) {
            return redirect()->route('home');
        }

        $total = $this->calcularTotal($carrinho);

        return view('pedidos.checkout', compact('carrinho', 'total'));
    }

    /**
     * Processa a finalização do pedido e salva no banco de dados
     */
    public function finalizarPedido(Request $request)
    {
        $carrinho = session()->get('carrinho', []);

        if (empty($carrinho)) {
            return redirect()->route('home')->with('erro', 'Seu carrinho está vazio.');
        }

        // 1. Validação dos novos campos individuais de endereço
        $request->validate([
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'estado' => 'required',
        ]);

        // 2. Montagem da string de endereço completo para salvar na coluna 'endereco'
        $enderecoConcatenado = "{$request->rua}, {$request->numero} - {$request->bairro}, {$request->cidade}/{$request->estado}";

        if ($request->complemento) {
            $enderecoConcatenado .= " ({$request->complemento})";
        }

        DB::beginTransaction();

        try {
            // 3. Criar o Pedido (Cabeçalho)
            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'total' => collect($carrinho)->sum(fn($item) => $item['preco'] * $item['quantidade']),
                'status' => 'pendente',
                'nome_entrega' => Auth::user()->name,
                'cpf_entrega' => preg_replace('/\D/', '', Auth::user()->cpf),
                'cep' => preg_replace('/\D/', '', $request->cep),
                'endereco' => $enderecoConcatenado,
            ]);

            // 4. Criar os Itens do Pedido (Relacionamento)
            foreach ($carrinho as $id => $detalhes) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $id,
                    'quantidade' => $detalhes['quantidade'],
                    'preco_unitario' => $detalhes['preco'],
                ]);
            }

            DB::commit();

            // 5. Limpar a sessão do carrinho após o sucesso
            session()->forget('carrinho');

            return redirect()->route('pedido.sucesso', $pedido->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('erro', 'Falha ao processar pedido: ' . $e->getMessage());
        }
    }
}
