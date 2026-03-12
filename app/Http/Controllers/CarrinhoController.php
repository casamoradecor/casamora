<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        if (!$request->produto_id) {
            return response()->json(['success' => false, 'message' => 'ID ausente'], 400);
        }

        $produto = Produto::find($request->produto_id);

        if (!$produto) {
            return response()->json(['success' => false, 'message' => 'Produto não encontrado'], 404);
        }

        $quantidadeSolicitada = (int) $request->input('quantidade', 1);
        $carrinho = session()->get('carrinho', []);
        $quantidadeJaNoCarrinho = isset($carrinho[$produto->id]) ? $carrinho[$produto->id]['quantidade'] : 0;
        $totalFinal = $quantidadeJaNoCarrinho + $quantidadeSolicitada;

        if ($totalFinal > $produto->estoque) {
            return response()->json([
                'success' => false,
                'message' => "Estoque insuficiente. Temos apenas {$produto->estoque} unidades em estoque."
            ], 400);
        }

        // Tratamento da URL da Imagem
        $caminho = $produto->imagem;
        if ($caminho && str_contains($caminho, 'assets')) {
            $urlFinal = asset(ltrim($caminho, '/'));
        } else {
            $urlFinal = $caminho ? Storage::url($caminho) : asset('assets/vasomora.png');
        }

        // Atualização da Sessão
        if(isset($carrinho[$produto->id])) {
            $carrinho[$produto->id]['quantidade'] += $quantidadeSolicitada;
        } else {
            $carrinho[$produto->id] = [
                "id" => $produto->id,
                "nome" => $produto->nome,
                "quantidade" => $quantidadeSolicitada,
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
        if(empty($carrinho)) return redirect()->route('home');

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

        $request->validate([
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'estado' => 'required',
        ]);

        DB::beginTransaction();

        try {
            foreach ($carrinho as $id => $item) {
                $produtoBanco = Produto::lockForUpdate()->find($id);

                if (!$produtoBanco || $produtoBanco->estoque < $item['quantidade']) {
                    throw new \Exception("Desculpe, o produto '{$item['nome']}' não possui estoque suficiente para finalizar a compra.");
                }
                $produtoBanco->decrement('estoque', $item['quantidade']);
            }
            $enderecoCompleto = "{$request->rua}, {$request->numero} - {$request->bairro}, {$request->cidade}/{$request->estado}";
            if ($request->complemento) $enderecoCompleto .= " ({$request->complemento})";

            // CRIAR PEDIDO
            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'total' => $this->calcularTotal($carrinho),
                'status' => 'pendente',
                'nome_entrega' => Auth::user()->name,
                'cpf_entrega' => preg_replace('/\D/', '', Auth::user()->cpf),
                'cep' => preg_replace('/\D/', '', $request->cep),
                'endereco' => $enderecoCompleto,
            ]);

            //Criar os Itens do Pedido (Relacionamento)
            foreach ($carrinho as $id => $detalhes) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $id,
                    'quantidade' => $detalhes['quantidade'],
                    'preco_unitario' => $detalhes['preco'],
                ]);
            }

            DB::commit();
            session()->forget('carrinho');

            return redirect()->route('pedido.sucesso', $pedido->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('erro', $e->getMessage());
        }
    }
}
