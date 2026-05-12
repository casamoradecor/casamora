<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Pedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MelhorEnvioService
{
    protected $url;
    protected $token;
    protected $cepOrigem;

    public function __construct()
    {
        $this->url = rtrim((string) env('MELHOR_ENVIO_URL'), '/');
        $this->token = (string) env('MELHOR_ENVIO_TOKEN');
        $this->cepOrigem = preg_replace('/\D/', '', (string) env('CEP_ORIGEM', '09530401'));
    }

    public function adicionarAoCarrinho(Pedido $pedido)
    {
        $produtosParaEnvio = [];
        $pesoTotalItens = 0;
        $maiorAltura = 20;
        $maiorLargura = 20;
        $maiorComprimento = 20;
        $pesoDaEmbalagem = 0.3;

        foreach ($pedido->itens as $item) {
            $produto = $item->produto;
            $produtosParaEnvio[] = [
                'name' => $produto->nome,
                'quantity' => $item->quantidade,
                'unitary_value' => $item->preco_unitario
            ];
            $pesoTotalItens += ((float) $produto->peso * $item->quantidade);
            if ((float) $produto->altura > $maiorAltura) $maiorAltura = (float) $produto->altura;
            if ((float) $produto->largura > $maiorLargura) $maiorLargura = (float) $produto->largura;
            if ((float) $produto->comprimento > $maiorComprimento) $maiorComprimento = (float) $produto->comprimento;
        }
        $pesoFinal = $pesoTotalItens + $pesoDaEmbalagem;
        $volumes = [
            [
                'width'  => $maiorLargura,
                'height' => $maiorAltura,
                'length' => $maiorComprimento,
                'weight' => $pesoFinal
            ]
        ];

        $payload = [
            'service' => $pedido->servico_frete_id,
            'agency'  => 4862,
            'from' => [
                'name'    => 'Kathia Gonzalez',
                'company' => 'Casa MORÁ',
                'email'   => 'contato@casamora.com',
                'phone'   => '11950352836',
                'document'=> '56728594027',
                'address' => 'Rua Roberto Simonssen',
                'number'  => '1014',
                'district'=> 'Santo Antônio',
                'city'    => 'São Caetano do Sul',
                'state_abbr' => 'SP',
                'postal_code'=> '09530401'
            ],
            'to' => [
                'name'     => $pedido->nome_entrega,
                'document' => preg_replace('/[^0-9]/', '', $pedido->cpf_entrega),
                'email'    => $pedido->cliente->email,
                'address'  => $pedido->endereco,
                'number'   => 'S/N',
                'district' => $pedido->bairro ?? 'Bairro',
                'city'     => $pedido->cidade ?? 'Cidade',
                'state_abbr' => $pedido->estado ?? 'SP',
                'postal_code'=> str_replace('-', '', $pedido->cep),
                'phone'    => preg_replace('/[^0-9]/', '', $pedido->cliente->telefone)
            ],
            'products' => $produtosParaEnvio,
            'volumes'  => $volumes,
            'options'  => [
                'insurance_value' => $pedido->valor_produtos,
                'non_commercial' => true
            ]
        ];

        $response = Http::withToken($this->token)
            ->acceptJson()
            ->post("{$this->url}/api/v2/me/cart", $payload);

        if (!$response->successful()) {
            dd([
                'ERRO_API' => $response->json(),
                'DADOS_ENVIADOS' => $payload
            ]);
        }

        return $response->json();
    }

    public function finalizarCompra($orderId)
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->post("{$this->url}/api/v2/me/shipment/checkout", ['orders' => [$orderId]])
            ->json();
    }

    public function gerarEtiqueta($orderId)
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->post("{$this->url}/api/v2/me/shipment/print", [
                'orders' => [$orderId]
            ])
            ->json();
    }

    public function calcularFrete($cepDestino, $produtos) {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->post("{$this->url}/api/v2/me/shipment/calculate", [
                "from" => ["postal_code" => $this->cepOrigem],
                "to"   => ["postal_code" => str_replace('-', '', $cepDestino)],
                "products" => $produtos
            ]);

        return $response->successful() ? $response->json() : [];
    }

    public function calcularOpcoesParaProduto(string $cepDestino, Produto $produto): array
    {
        $this->validarCep($cepDestino);

        return $this->calcularOpcoesNormalizadas($cepDestino, [[
            'id' => $produto->id,
            'width' => (float) ($produto->largura ?: 15),
            'height' => (float) ($produto->altura ?: 15),
            'length' => (float) ($produto->comprimento ?: 15),
            'weight' => (float) ($produto->peso ?: 0.5),
            'insurance_value' => (float) $produto->preco,
            'quantity' => 1,
        ]]);
    }

    public function calcularOpcoesParaCarrinho(string $cepDestino, array $carrinho): array
    {
        $this->validarCep($cepDestino);

        $produtos = $this->montarProdutosDoCarrinho($carrinho);

        if (empty($produtos)) {
            return [];
        }

        return $this->calcularOpcoesNormalizadas($cepDestino, $produtos);
    }

    public function obterOpcaoCarrinhoPorId(string $cepDestino, array $carrinho, string $servicoId): ?array
    {
        $opcoes = $this->calcularOpcoesParaCarrinho($cepDestino, $carrinho);

        foreach ($opcoes as $opcao) {
            if ((string) $opcao['id'] === (string) $servicoId) {
                return $opcao;
            }
        }

        return null;
    }

    protected function validarCep(string &$cepDestino): void
    {
        $cepDestino = preg_replace('/\D/', '', $cepDestino);

        if (strlen($cepDestino) !== 8) {
            throw ValidationException::withMessages([
                'cep' => 'CEP invalido.',
            ]);
        }
    }

    protected function montarProdutosDoCarrinho(array $carrinho): array
    {
        $produtosApi = [];

        foreach ($carrinho as $id => $item) {
            $quantidade = (int) ($item['quantidade'] ?? 0);
            if ($quantidade <= 0) {
                continue;
            }

            $produto = Produto::find($id);
            if (!$produto) {
                continue;
            }

            $produtosApi[] = [
                'id' => $produto->id,
                'width' => (float) ($produto->largura ?: 15),
                'height' => (float) ($produto->altura ?: 15),
                'length' => (float) ($produto->comprimento ?: 15),
                'weight' => (float) ($produto->peso ?: 0.5),
                'insurance_value' => (float) $produto->preco,
                'quantity' => $quantidade,
            ];
        }

        return $produtosApi;
    }

    protected function calcularOpcoesNormalizadas(string $cepDestino, array $produtosApi): array
    {
        $opcoes = [];
        $precoReferenciaBase = null;

        if ($this->token !== '') {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->timeout(10)
                ->post("{$this->url}/api/v2/me/shipment/calculate", [
                    'from' => ['postal_code' => $this->cepOrigem],
                    'to' => ['postal_code' => $cepDestino],
                    'products' => $produtosApi,
                ]);

            if ($response->successful()) {
                foreach ($response->json() as $servico) {
                    if (!isset($servico['name']) || isset($servico['error'])) {
                        continue;
                    }

                    $preco = (float) $servico['price'];

                    if ($precoReferenciaBase === null) {
                        $precoReferenciaBase = $preco;
                    }

                    $opcoes[] = [
                        'id' => (string) $servico['id'],
                        'nome' => $servico['name'],
                        'valor' => $preco,
                        'preco' => number_format($preco, 2, ',', '.'),
                        'prazo' => ($servico['delivery_range']['max'] ?? '?') . ' dias uteis',
                        'icone' => 'fa-truck',
                    ];
                }
            } else {
                Log::warning('Melhor Envio retornou falha no calculo de frete.', [
                    'status' => $response->status(),
                ]);
            }
        }

        return $this->adicionarEntregaLocal($opcoes, $precoReferenciaBase, $cepDestino);
    }

    protected function adicionarEntregaLocal(array $opcoes, ?float $precoReferenciaBase, string $cepDestino): array
    {
        $prefixo = substr($cepDestino, 0, 2);
        $regioesLocais = ['01', '02', '03', '04', '05', '06', '07', '08', '09'];

        if (!in_array($prefixo, $regioesLocais, true)) {
            return $opcoes;
        }

        $valorUber = $precoReferenciaBase ? ($precoReferenciaBase - 5) : 20.00;
        if ($valorUber < 10) {
            $valorUber = 10.00;
        }

        array_unshift($opcoes, [
            'id' => '99',
            'nome' => 'Entrega Flash (Uber/Mora)',
            'valor' => $valorUber,
            'preco' => number_format($valorUber, 2, ',', '.'),
            'prazo' => 'Ate 24h',
            'icone' => 'fa-bolt',
        ]);

        return $opcoes;
    }
}
