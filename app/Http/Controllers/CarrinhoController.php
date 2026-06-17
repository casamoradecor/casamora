<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CarrinhoController extends Controller
{
    public function index()
    {
        return view('pedidos.carrinho');
    }

    /**
     * Retorna os dados atuais do carrinho para o JS (Sidebar)
     */
    public function listar()
    {
        $resumo = $this->montarResumoCarrinho(session()->get('carrinho', []), true);

        return response()->json([
            'itens' => $resumo['itens'],
            'subtotal' => $resumo['subtotal'],
            'desconto' => $resumo['desconto'],
            'total' => $resumo['total'],
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
            return response()->json(['success' => false, 'message' => 'Produto nao encontrado'], 404);
        }

        $quantidadeSolicitada = (int) $request->input('quantidade', 1);
        $carrinho = session()->get('carrinho', []);
        $quantidadeJaNoCarrinho = isset($carrinho[$produto->id]) ? (int) $carrinho[$produto->id]['quantidade'] : 0;
        $totalFinal = $quantidadeJaNoCarrinho + $quantidadeSolicitada;

        if ($quantidadeSolicitada <= 0) {
            return response()->json(['success' => false, 'message' => 'Quantidade invalida'], 400);
        }

        if ($totalFinal > $produto->estoque) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque insuficiente.',
            ], 400);
        }

        if (isset($carrinho[$produto->id])) {
            $carrinho[$produto->id]['quantidade'] += $quantidadeSolicitada;
        } else {
            $carrinho[$produto->id] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'quantidade' => $quantidadeSolicitada,
                'preco' => $produto->preco,
                'imagem' => $this->resolverImagemProduto($produto),
            ];
        }

        $resumo = $this->montarResumoCarrinho($carrinho, true);

        return response()->json([
            'success' => true,
            'itens' => $resumo['itens'],
            'total' => $resumo['total'],
        ]);
    }

    public function diminuir(Request $request)
    {
        $id = $request->produto_id;
        $carrinho = session()->get('carrinho', []);

        if (!isset($carrinho[$id])) {
            return response()->json(['success' => false, 'message' => 'PRODUTO NAO ENCONTRADO'], 404);
        }

        if ($carrinho[$id]['quantidade'] > 1) {
            $carrinho[$id]['quantidade']--;
        } else {
            unset($carrinho[$id]);
        }

        $resumo = $this->montarResumoCarrinho($carrinho, true);

        return response()->json([
            'success' => true,
            'itens' => $resumo['itens'],
            'total' => $resumo['total'],
        ]);
    }

    /**
     * Atualiza a quantidade diretamente na tela de Checkout
     */
    public function atualizarQtd(Request $request)
    {
        $id = $request->produto_id;
        $variacao = (int) $request->variacao;
        $carrinho = session()->get('carrinho', []);

        if (!isset($carrinho[$id])) {
            return response()->json(['error' => 'Produto nao encontrado'], 404);
        }

        $novaQuantidade = (int) $carrinho[$id]['quantidade'] + $variacao;

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

        $this->montarResumoCarrinho($carrinho, true);

        return response()->json(['success' => true]);
    }

    /**
     * Exibe a tela de Checkout
     */
    public function checkout($id = null)
    {
        if ($id) {
            $pedido = Pedido::with('itens.produto')
                ->where('cliente_id', Auth::id())
                ->where('status', 'pendente')
                ->findOrFail($id);

            $novoCarrinho = [];

            foreach ($pedido->itens as $item) {
                $produto = $item->produto;

                if (!$produto) {
                    continue;
                }

                $novoCarrinho[$produto->id] = [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'quantidade' => $item->quantidade,
                    'preco' => $item->preco_unitario,
                    'imagem' => $this->resolverImagemProduto($produto),
                ];
            }

            session()->put('carrinho', $novoCarrinho);
        }

        $resumo = $this->montarResumoCarrinho(session()->get('carrinho', []), true);

        if (empty($resumo['itens'])) {
            return redirect()->route('home');
        }

        return view('pedidos.checkout', [
            'carrinho' => $resumo['itens'],
            'total' => $resumo['total'],
            'subtotal' => $resumo['subtotal'],
            'desconto' => $resumo['desconto'],
            'id' => $id,
        ]);
    }

    /**
     * PROCESSO DE FINALIZACAO E INTEGRACAO MERCADO PAGO
     */
    public function finalizarPedido(Request $request, MelhorEnvioService $melhorEnvio)
    {
        $carrinhoSessao = session()->get('carrinho', []);
        $resumo = $this->montarResumoCarrinho($carrinhoSessao, true);

        if (empty($resumo['itens'])) {
            return redirect()->route('home')->with('erro', 'Carrinho vazio.');
        }

        $request->validate([
            'pedido_id' => 'nullable|integer|exists:pedidos,id',
            'cep' => 'required|string|max:10',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'frete_escolhido' => 'required|string',
            'servico_frete_id' => 'required|string',
        ]);

        try {
            $cepDestino = preg_replace('/\D/', '', $request->cep);
            $freteSelecionado = $melhorEnvio->obterOpcaoCarrinhoPorId(
                $cepDestino,
                $carrinhoSessao,
                (string) $request->servico_frete_id
            );

            if (!$freteSelecionado) {
                throw ValidationException::withMessages([
                    'frete_escolhido' => 'A opcao de frete informada e invalida ou expirou.',
                ]);
            }

            DB::beginTransaction();

            $user = Auth::user();
            $enderecoTexto = "{$request->rua}, {$request->numero} - {$request->bairro}, {$request->cidade}/{$request->estado}";

            $enderecoDb = Endereco::updateOrCreate(
                [
                    'cliente_id' => $user->id,
                    'cep' => $cepDestino,
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

            foreach ($resumo['itens'] as $id => $item) {
                $produto = Produto::lockForUpdate()->find($id);

                if (!$produto || $produto->estoque < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para: {$item['nome']}");
                }

                $produto->decrement('estoque', $item['quantidade']);
            }

            $valorFrete = (float) $freteSelecionado['valor'];
            $valorTotal = $resumo['total'] + $valorFrete;

            $dadosPedido = [
                'cliente_id' => $user->id,
                'endereco_id' => $enderecoDb->id,
                'valor_produtos' => $resumo['subtotal'],
                'valor_desconto' => $resumo['desconto'],
                'valor_frete' => $valorFrete,
                'valor_total' => $valorTotal,
                'status' => 'pendente',
                'codigo_externo' => 'MOR-' . time(),
                'nome_entrega' => $user->name,
                'cpf_entrega' => preg_replace('/\D/', '', (string) $user->cpf),
                'cep' => $cepDestino,
                'endereco' => $enderecoTexto,
                'servico_frete_id' => (string) $freteSelecionado['id'],
                'metodo_envio' => $freteSelecionado['nome'],
            ];

            if ($request->filled('pedido_id')) {
                $pedido = Pedido::where('cliente_id', $user->id)
                    ->where('status', 'pendente')
                    ->findOrFail($request->pedido_id);

                $pedido->update($dadosPedido);

                PedidoItem::where('pedido_id', $pedido->id)->delete();
                Pagamento::where('pedido_id', $pedido->id)->where('status', 'pending')->delete();
            } else {
                $pedido = Pedido::create($dadosPedido);
            }

            foreach ($resumo['itens'] as $id => $item) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'subtotal' => $item['preco'] * $item['quantidade'],
                ]);
            }

            Pagamento::create([
                'pedido_id' => $pedido->id,
                'metodo' => 'pix',
                'status' => 'pending',
                'valor_pago' => 0,
            ]);

            $itensMp = [];
            foreach ($resumo['itens'] as $item) {
                $itensMp[] = [
                    'title' => $item['nome'],
                    'quantity' => (int) $item['quantidade'],
                    'unit_price' => (float) $item['preco'],
                ];
            }

            if ($resumo['desconto'] > 0) {
                $itensMp[] = [
                    'title' => 'Desconto aplicado',
                    'quantity' => 1,
                    'unit_price' => (float) ($resumo['desconto'] * -1),
                ];
            }

            if ($valorFrete > 0) {
                $itensMp[] = [
                    'title' => 'Frete: ' . $freteSelecionado['nome'],
                    'quantity' => 1,
                    'unit_price' => $valorFrete,
                ];
            }

            $mpConfig = config('services.mercadopago');

            $mpResponse = Http::withToken($mpConfig['token'])
                ->acceptJson()
                ->connectTimeout($mpConfig['connect_timeout'])
                ->timeout($mpConfig['timeout'])
                ->post(rtrim($mpConfig['base_url'], '/') . '/checkout/preferences', [
                    'items' => $itensMp,
                    'payer' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'back_urls' => [
                        'success' => url('/pedido/sucesso/' . $pedido->id),
                        'failure' => url('/checkout'),
                        'pending' => url('/pedido/sucesso/' . $pedido->id),
                    ],
                    'notification_url' => url('/webhook/mercadopago?source_news=webhooks'),
                    'external_reference' => (string) $pedido->id,
                    'statement_descriptor' => 'CASA MORA',
                    'expires' => false,
                ]);

            if ($mpResponse->failed()) {
                Log::error('Falha ao criar preferencia no Mercado Pago.', [
                    'pedido_id' => $pedido->id,
                    'user_id' => $user->id,
                    'http_status' => $mpResponse->status(),
                    'error_message' => $mpResponse->json()['message'] ?? 'Erro desconhecido na API do gateway.'
                ]);

                throw new \RuntimeException('Nao foi possivel iniciar o pagamento no momento.');
            }

            DB::commit();
            session()->forget('carrinho');

            return redirect()->away($mpResponse->json()['init_point']);
        } catch (ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $e;
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Falha ao finalizar pedido.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Nao foi possivel concluir seu pedido agora. Tente novamente.');
        }
    }

    /**
     * Tela de Sucesso
     */
    public function pedidoSucesso($id)
    {
        $pedido = Pedido::where('cliente_id', Auth::id())->findOrFail($id);

        return view('pedidos.pedido-sucesso', compact('pedido'));
    }

    private function montarResumoCarrinho(array $carrinho, bool $sincronizarSessao = false): array
    {
        $itens = [];
        $subtotal = 0.0;

        foreach ($carrinho as $id => $item) {
            $produto = Produto::find($id);
            $quantidade = (int) ($item['quantidade'] ?? 0);

            if (!$produto || $quantidade <= 0) {
                continue;
            }

            $precoAtual = (float) $produto->preco;

            $itens[$produto->id] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'quantidade' => $quantidade,
                'preco' => $precoAtual,
                'imagem' => $this->resolverImagemProduto($produto),
            ];

            $subtotal += $precoAtual * $quantidade;
        }

        $desconto = $this->calcularDesconto($itens, $subtotal);
        $total = max($subtotal - $desconto, 0);

        if ($sincronizarSessao) {
            session()->put('carrinho', $itens);
        }

        return [
            'itens' => $itens,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $total,
        ];
    }

    private function calcularDesconto(array $itens, float $subtotal): float
    {
        // Estrutura preparada para cupons, promocoes e outras regras futuras.
        return 0.0;
    }

    private function resolverImagemProduto(Produto $produto): string
    {
        $caminho = $produto->imagem;

        return ($caminho && str_contains($caminho, 'assets'))
            ? asset(ltrim($caminho, '/'))
            : ($caminho ? Storage::url($caminho) : asset('assets/vasomora.png'));
    }
}
