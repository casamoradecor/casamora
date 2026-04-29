<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Pedido;

class MelhorEnvioService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = env('MELHOR_ENVIO_URL');
        $this->token = env('MELHOR_ENVIO_TOKEN');
    }

    public function adicionarAoCarrinho(Pedido $pedido)
    {
        $produtosParaEnvio = [];
        $volumes = [];
        foreach ($pedido->itens as $item) {
            $produto = $item->produto;
            $produtosParaEnvio[] = [
                'name' => $produto->nome,
                'quantity' => $item->quantidade,
                'unitary_value' => $item->preco_unitario
            ];

            $volumes[] = [
                'width'  => (float) $produto->largura,
                'height' => (float) $produto->altura,
                'length' => (float) $produto->comprimento,
                'weight' => (float) $produto->peso
            ];
        }

        $payload = [
            'service' => 3,
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
                "from" => ["postal_code" => env('CEP_ORIGEM')],
                "to"   => ["postal_code" => str_replace('-', '', $cepDestino)],
                "products" => $produtos
            ]);

        return $response->successful() ? $response->json() : [];
    }
}
