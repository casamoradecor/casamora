<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Endereco;
use App\Models\Pagamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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

        $quantidadeSolicitada = (int)$request->input('quantidade', 1);
        $carrinho = session()->get('carrinho', []);
        $quantidadeJaNoCarrinho = isset($carrinho[$produto->id]) ? $carrinho[$produto->id]['quantidade'] : 0;
        $totalFinal = $quantidadeJaNoCarrinho + $quantidadeSolicitada;

        if ($totalFinal > $produto->estoque) {
            return response()->json([
                'success' => false,
                'message' => "Estoque insuficiente. Temos apenas {$produto->estoque} unidades."
            ], 400);
        }

        $caminho = $produto->imagem;
        $urlFinal = ($caminho && str_contains($caminho, 'assets'))
            ? asset(ltrim($caminho, '/'))
            : ($caminho ? Storage::url($caminho) : asset('assets/vasomora.png'));

        if (isset($carrinho[$produto->id])) {
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

    public function diminuir(Request $request)
    {
        $id = $request->produto_id;
        $carrinho = session()->get('carrinho', []);

        if (isset($carrinho[$id])) {
            // Se a quantidade for maior que 1, apenas diminui
            if ($carrinho[$id]['quantidade'] > 1) {
                $carrinho[$id]['quantidade']--;
            } else {
                // Se for a última unidade, remove do carrinho
                unset($carrinho[$id]);
            }

            session()->put('carrinho', $carrinho);

            return response()->json([
                'success' => true,
                'itens' => $carrinho,
                'total' => $this->calcularTotal($carrinho)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'PRODUTO NÃO ENCONTRADO'], 404);
    }

    /**
     * Atualiza a quantidade diretamente na tela de Checkout
     */
    public function atualizarQtd(Request $request)
    {
        $id = $request->produto_id;
        $variacao = (int)$request->variacao;
        $carrinho = session()->get('carrinho', []);

        if (isset($carrinho[$id])) {
            $novaQuantidade = $carrinho[$id]['quantidade'] + $variacao;

            if ($novaQuantidade <= 0) {
                unset($carrinho[$id]);
            } else {
                if ($variacao > 0) {
                    $produto = Produto::find($id);
                    if ($produto && $novaQuantidade > $produto->estoque) {
                        return response()->json(['error' => 'Estoque insuficiente'], 400);
                    }
                }
                $carrinho[$id]['quantidade'] = $novaQuantidade;
            }

            session()->put('carrinho', $carrinho);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Produto não encontrado'], 404);
    }

    /**
     * Exibe a tela de Checkout
     */
    public function checkout()
    {
        $carrinho = session()->get('carrinho', []);
        if (empty($carrinho)) return redirect()->route('home');

        $total = $this->calcularTotal($carrinho);
        return view('pedidos.checkout', compact('carrinho', 'total'));
    }

    /**
     * PROCESSO DE FINALIZAÇÃO E INTEGRAÇÃO MERCADO PAGO
     */
    public function finalizarPedido(Request $request)
    {
        $carrinho = session()->get('carrinho', []);
        if (empty($carrinho)) return redirect()->route('home')->with('erro', 'Carrinho vazio.');

        $request->validate([
            'cep' => 'required', 'rua' => 'required', 'numero' => 'required',
            'bairro' => 'required', 'cidade' => 'required', 'estado' => 'required',
            'frete_escolhido' => 'required', 'valor_frete' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $enderecoTexto = "{$request->rua}, {$request->numero} - {$request->bairro}, {$request->cidade}/{$request->estado}";

            // 1. Criar ou Buscar Endereço (EVITA DUPLICADOS)
            $enderecoDb = Endereco::updateOrCreate(
                [
                    'cliente_id' => $userId,
                    'cep' => preg_replace('/\D/', '', $request->cep),
                    'numero' => $request->numero,
                ],
                [
                    'logradouro' => $request->rua,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'complemento' => $request->complemento,
                ]
            );

            $valorProdutos = 0;
            $itensMp = [];

            // 2. Validar Estoque e Preparar Itens para MP
            foreach ($carrinho as $id => $item) {
                $produto = Produto::lockForUpdate()->find($id);
                if (!$produto || $produto->estoque < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para: {$item['nome']}");
                }
                $produto->decrement('estoque', $item['quantidade']);
                $valorProdutos += ($item['preco'] * $item['quantidade']);

                $itensMp[] = [
                    'title' => $item['nome'],
                    'quantity' => (int)$item['quantidade'],
                    'unit_price' => (float)$item['preco']
                ];
            }

            $valorFrete = (float)$request->valor_frete;

            // 3. Criar o Pedido
            $pedido = Pedido::create([
                'cliente_id' => $userId,
                'endereco_id' => $enderecoDb->id,
                'valor_produtos' => $valorProdutos,
                'valor_frete' => $valorFrete,
                'valor_total' => $valorProdutos + $valorFrete,
                'status' => 'pendente',
                'codigo_externo' => 'MOR-' . time(),
                'nome_entrega' => Auth::user()->name,
                'cpf_entrega' => preg_replace('/\D/', '', Auth::user()->cpf),
                'cep' => preg_replace('/\D/', '', $request->cep),
                'endereco' => $enderecoTexto,
            ]);

            // 4. Criar Itens do Pedido
            foreach ($carrinho as $id => $detalhes) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $id,
                    'quantidade' => $detalhes['quantidade'],
                    'preco_unitario' => $detalhes['preco'],
                    'subtotal' => $detalhes['quantidade'] * $detalhes['preco'],
                ]);
            }

            // 5. Registro de Pagamento Local
            Pagamento::create([
                'pedido_id' => $pedido->id,
                'metodo' => 'pix',
                'status' => 'pending',
                'valor_pago' => 0
            ]);

            // Adicionar Frete ao MP
            if ($valorFrete > 0) {
                $itensMp[] = [
                    'title' => 'Frete: ' . $request->frete_escolhido,
                    'quantity' => 1,
                    'unit_price' => $valorFrete
                ];
            }

            // 6. Chamada Mercado Pago (Forçando URL absoluta)
            // Dentro do método finalizarPedido, onde você faz o Http::post
            $mpResponse = Http::withToken(env('MERCADOPAGO_ACCESS_TOKEN'))
                ->post('https://api.mercadopago.com/checkout/preferences', [
                    'items' => $itensMp,
                    'payer' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email, // CAMPO ESSENCIAL
                    ],
                    'back_urls' => [
                        'success' => url('/pedido/sucesso/' . $pedido->id),
                        'failure' => url('/checkout'),
                        'pending' => url('/pedido/sucesso/' . $pedido->id),
                    ],
                    'notification_url' => url('/webhook/mercadopago'),
                    'external_reference' => (string)$pedido->id,
                    'statement_descriptor' => 'CASA MORA',
                    'expires' => false,
                ]);

            if ($mpResponse->failed()) {
                throw new \Exception('Erro ao comunicar com Mercado Pago: ' . $mpResponse->body());
            }

            DB::commit();
            session()->forget('carrinho');

            return redirect()->away($mpResponse->json()['init_point']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('erro', $e->getMessage());
        }
    }

    /**
     * Tela de Sucesso
     */
    public function pedidoSucesso($id)
    {
        $pedido = Pedido::findOrFail($id);
        return view('pedidos.pedido-sucesso', compact('pedido'));
    }

    /**
     * Função privada para cálculo de total
     */
    private function calcularTotal($carrinho)
    {
        $total = 0;
        foreach ($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
}
