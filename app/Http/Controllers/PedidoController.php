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

        $request->validate([
            'cep' => 'required|string|max:10',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'servico_frete_id' => 'required|string',
        ]);

        try {
            $melhorEnvio = app(\App\Services\MelhorEnvioService::class);
            $cepDestino = preg_replace('/\D/', '', $request->cep);

            $freteOficial = $melhorEnvio->obterOpcaoCarrinhoPorId(
                $cepDestino,
                $carrinho,
                (string) $request->servico_frete_id
            );

            if (!$freteOficial) {
                return back()->with('erro', 'A opção de frete selecionada é inválida ou expirou.');
            }

            return DB::transaction(function () use ($request, $user, $carrinho, $freteOficial, $cepDestino) {

                foreach ($carrinho as $idProduto => $item) {
                    $produto = \App\Models\Produto::lockForUpdate()->find($idProduto);

                    if (!$produto || $produto->estoque < $item['quantidade']) {
                        throw new \Exception("Estoque insuficiente para o produto: " . ($produto->nome ?? 'Item indisponível'));
                    }

                    $produto->decrement('estoque', $item['quantidade']);
                }

                $enderecoObj = Endereco::firstOrCreate(
                    [
                        'cliente_id'  => $user->id,
                        'cep'         => $cepDestino,
                        'numero'      => $request->numero,
                        'complemento' => $request->complemento,
                    ],
                    [
                        'logradouro' => $request->rua,
                        'bairro'     => $request->bairro,
                        'cidade'     => $request->cidade,
                        'estado'     => $request->estado,
                    ]
                );

                // 4. Cálculos Seguros de Valores baseados no Banco de Dados
                $valorProdutos = 0;
                foreach ($carrinho as $idProduto => $item) {
                    $produto = \App\Models\Produto::find($idProduto);
                    $valorProdutos += $produto->preco * $item['quantidade'];
                }

                // O valor do frete agora vem da API externa auditada pelo PHP, impossível de fraudar no front-end
                $valorFrete = (float) $freteOficial['valor'];
                $valorTotal = $valorProdutos + $valorFrete;

                // 5. Criar o Pedido
                $pedido = Pedido::create([
                    'cliente_id'     => $user->id,
                    'endereco_id'    => $enderecoObj->id,
                    'valor_produtos' => $valorProdutos,
                    'valor_frete'    => $valorFrete,
                    'valor_desconto' => 0,
                    'valor_total'    => $valorTotal,
                    'status'         => 'pendente',
                    'nome_entrega'   => $user->name,
                    'cpf_entrega'    => preg_replace('/\D/', '', (string) $user->cpf), // Higienização LGPD
                    'cep'            => $cepDestino,
                    'endereco'       => "{$request->rua}, {$request->numero} - {$request->bairro}. {$request->cidade}/{$request->estado}",
                    'servico_frete_id' => (string) $freteOficial['id'],
                    'metodo_envio'   => $freteOficial['nome'],
                ]);

                // 6. Salvar Itens
                foreach ($carrinho as $idProduto => $item) {
                    $produto = \App\Models\Produto::find($idProduto);
                    PedidoItem::create([
                        'pedido_id'      => $pedido->id,
                        'produto_id'     => $idProduto,
                        'quantidade'     => $item['quantidade'],
                        'preco_unitario' => $produto->preco,
                        'subtotal'       => $produto->preco * $item['quantidade'],
                    ]);
                }

                // 7. Finalização da Sessão
                session()->forget('carrinho');

                return redirect()->route('pagamento.checkout', $pedido->id);
            });

        } catch (\Exception $e) {
            Log::error('Falha no fluxo de criacao de pedido.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('erro', 'Não foi possível processar seu pedido agora. Verifique a disponibilidade dos itens.');
        }
    }
}
