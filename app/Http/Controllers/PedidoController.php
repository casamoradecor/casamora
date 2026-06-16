<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Endereco;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::where('cliente_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with(['itens.produto', 'pagamento'])
            ->where('cliente_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }

    public function finalizar(Request $request)
    {
        $user = Auth::user();
        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return redirect()->route('carrinho.index')->with('erro', 'Seu carrinho está vazio.');
        }

        try {
            return DB::transaction(function () use ($request, $user, $carrinho) {

                // 1. Lógica de Endereço (Sincronizada com Model Endereco)
                $enderecoObj = Endereco::firstOrCreate(
                    [
                        'cliente_id'  => $user->id,
                        'cep'         => $request->cep,
                        'numero'      => $request->numero,
                        'complemento' => $request->complemento,
                    ],
                    [
                        'logradouro' => $request->rua, // Mapeado do input 'rua' para 'logradouro'
                        'bairro'     => $request->bairro,
                        'cidade'     => $request->cidade,
                        'estado'     => $request->estado,
                    ]
                );

                // 2. Cálculos de Valores
                $valorProdutos = 0;
                foreach ($carrinho as $item) {
                    $valorProdutos += $item['preco'] * $item['quantidade'];
                }

                $valorFrete = (float) $request->valor_frete;
                $valorTotal = $valorProdutos + $valorFrete;

                // 3. Criar o Pedido (Sincronizado com Model Pedido)
                $pedido = Pedido::create([
                    'cliente_id'     => $user->id,
                    'endereco_id'    => $enderecoObj->id,
                    'valor_produtos' => $valorProdutos,
                    'valor_frete'    => $valorFrete,
                    'valor_desconto' => 0,
                    'valor_total'    => $valorTotal,
                    'status'         => 'pendente',
                    'nome_entrega'   => $user->name,
                    'cpf_entrega'    => $user->cpf,
                    'cep'            => $request->cep,
                    'endereco'       => "{$request->rua}, {$request->numero} - {$request->bairro}. {$request->cidade}/{$request->estado}",
                ]);

                // 4. Salvar Itens (Sincronizado com Model PedidoItem)
                foreach ($carrinho as $idProduto => $item) {
                    PedidoItem::create([
                        'pedido_id'      => $pedido->id,
                        'produto_id'     => $idProduto,
                        'quantidade'     => $item['quantidade'],
                        'preco_unitario' => $item['preco'],
                        'subtotal'       => $item['preco'] * $item['quantidade'],
                    ]);
                }

                // 5. Finalização
                session()->forget('carrinho');

                // Redireciona para o fluxo de pagamento
                return redirect()->route('pagamento.checkout', $pedido->id);
            });

        } catch (\Exception $e) {
            Log::error('Falha no fluxo legado de criacao de pedido.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('erro', 'Nao foi possivel processar seu pedido agora. Tente novamente.');
        }
    }
}
