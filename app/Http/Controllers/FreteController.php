<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FreteController extends Controller
{
    public function calcular(Request $request)
    {
        try {
            $cepDestino = preg_replace('/\D/', '', $request->cep);
            $produto = Produto::find($request->produto_id);

            if (!$produto) return response()->json(['error' => 'Produto não encontrado'], 404);

            $opcoesFrete = [];
            $precoReferenciaBase = null;
            $token = env('MELHOR_ENVIO_TOKEN');
            $urlBase = rtrim(env('MELHOR_ENVIO_URL'), '/');

            if ($token) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->post($urlBase . '/api/v2/me/shipment/calculate', [
                        "from" => ["postal_code" => "09530401"],
                        "to"   => ["postal_code" => $cepDestino],
                        "products" => [[
                            "id" => $produto->id,
                            "width" => (float)$produto->largura,
                            "height" => (float)$produto->altura,
                            "length" => (float)$produto->comprimento,
                            "weight" => (float)$produto->peso,
                            "insurance_value" => (float)$produto->preco,
                            "quantity" => 1
                        ]]
                    ]);

                if ($response->successful()) {
                    $servicos = $response->json();
                    foreach ($servicos as $s) {
                        if (isset($s['name']) && !isset($s['error'])) {
                            if ($precoReferenciaBase === null) {
                                $precoReferenciaBase = (float) $s['price'];
                            }

                            $opcoesFrete[] = [
                                'id'    => $s['id'],
                                'nome'  => $s['name'],
                                'preco' => number_format($s['price'], 2, ',', '.'),
                                'prazo' => $s['delivery_range']['max'] . ' dias úteis',
                                'icone' => 'fa-truck' // Ícone padrão para todas
                            ];
                        }
                    }
                }
            }

            $prefixo = substr($cepDestino, 0, 2);
            $regioesLocais = ['01', '02', '03', '04', '05', '06', '07', '08', '09'];

            if (in_array($prefixo, $regioesLocais)) {
                $valorUber = $precoReferenciaBase ? ($precoReferenciaBase - 5) : 20.00;
                if ($valorUber < 10) $valorUber = 10.00;
                array_unshift($opcoesFrete, [
                    'id'    => 99,
                    'nome'  => 'Entrega Flash (Uber/Mora)',
                    'preco' => number_format($valorUber, 2, ',', '.'),
                    'prazo' => 'Até 24h',
                    'icone' => 'fa-bolt'
                ]);
            }

            return response()->json($opcoesFrete);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao calcular'], 500);
        }
    }

    public function calcularCarrinho(Request $request)
    {
        try {
            $cepDestino = preg_replace('/\D/', '', $request->cep);

            if (strlen($cepDestino) !== 8) {
                return response()->json(['error' => 'CEP inválido'], 400);
            }

            $carrinho = session()->get('carrinho', []);

            if (empty($carrinho)) {
                return response()->json(['error' => 'Carrinho vazio'], 400);
            }

            $produtosApi = [];

            foreach ($carrinho as $id => $item) {
                if (!isset($item['nome']) || $item['nome'] === 'NULL') continue;

                $produtoDb = Produto::find($id);
                if ($produtoDb) {
                    $produtosApi[] = [
                        "id" => $produtoDb->id,
                        "width" => (float)($produtoDb->largura ?: 15),
                        "height" => (float)($produtoDb->altura ?: 15),
                        "length" => (float)($produtoDb->comprimento ?: 15),
                        "weight" => (float)($produtoDb->peso ?: 0.5),
                        "insurance_value" => (float)$produtoDb->preco,
                        "quantity" => (int)$item['quantidade']
                    ];
                }
            }

            $opcoesFrete = [];
            $precoReferenciaBase = null;
            $token = env('MELHOR_ENVIO_TOKEN');
            $urlBase = rtrim(env('MELHOR_ENVIO_URL'), '/');

            if ($token && !empty($produtosApi)) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->post($urlBase . '/api/v2/me/shipment/calculate', [
                        "from" => ["postal_code" => "09530401"],
                        "to"   => ["postal_code" => $cepDestino],
                        "products" => $produtosApi
                    ]);

                if ($response->successful()) {
                    $servicos = $response->json();
                    foreach ($servicos as $s) {
                        if (isset($s['name']) && !isset($s['error'])) {

                            if ($precoReferenciaBase === null) {
                                $precoReferenciaBase = (float) $s['price'];
                            }

                            $opcoesFrete[] = [
                                'id'    => $s['id'],
                                'nome'  => $s['name'],
                                'preco' => number_format($s['price'], 2, ',', '.'),
                                'prazo' => $s['delivery_range']['max'] . ' dias úteis',
                                'icone' => 'fa-truck'
                            ];
                        }
                    }
                }
            }

            $prefixo = substr($cepDestino, 0, 2);
            $regioesLocais = ['01', '02', '03', '04', '05', '06', '07', '08', '09'];

            if (in_array($prefixo, $regioesLocais)) {
                $valorUber = $precoReferenciaBase ? ($precoReferenciaBase - 5) : 20.00;
                if ($valorUber < 10) $valorUber = 10.00;

                array_unshift($opcoesFrete, [
                    'id'    => 99,
                    'nome'  => 'Entrega Flash (Uber/Mora)',
                    'preco' => number_format($valorUber, 2, ',', '.'),
                    'prazo' => 'Até 24h',
                    'icone' => 'fa-bolt'
                ]);
            }

            return response()->json($opcoesFrete);

        } catch (\Exception $e) {
            Log::error("Erro Frete Checkout: " . $e->getMessage());
            return response()->json(['error' => 'Falha ao calcular'], 500);
        }
    }
}
