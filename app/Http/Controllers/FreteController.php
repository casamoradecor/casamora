<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Http;

class FreteController extends Controller
{
    public function calcular(Request $request)
    {
        try {
            $cepDestino = preg_replace('/\D/', '', $request->cep);
            $produto = Produto::find($request->produto_id);

            if (!$produto) return response()->json(['error' => 'Produto não encontrado'], 404);

            $opcoesFrete = [];
            $precoSedexBase = null;

            // 1. CONSULTA A API PRIMEIRO (Para pegar o valor do SEDEX)
            if (env('MELHOR_ENVIO_TOKEN')) {
                $response = Http::withToken(env('MELHOR_ENVIO_TOKEN'))
                    ->post(env('MELHOR_ENVIO_URL') . '/me/shipment/calculate', [
                        "from" => ["postal_code" => "09530401"],
                        "to"   => ["postal_code" => $cepDestino],
                        "products" => [[
                            "id" => $produto->id,
                            "width" => $produto->largura,
                            "height" => $produto->height ?? $produto->altura,
                            "length" => $produto->comprimento,
                            "weight" => $produto->peso,
                            "insurance_value" => $produto->preco,
                            "quantity" => 1
                        ]]
                    ]);

                if ($response->successful()) {
                    $servicos = $response->json();
                    foreach ($servicos as $s) {
                        if (isset($s['name']) && !isset($s['error'])) {
                            // Guardamos o preço se for SEDEX para usar no Uber depois
                            if (str_contains(strtoupper($s['name']), 'SEDEX')) {
                                $precoSedexBase = (float) $s['price'];
                            }

                            $opcoesFrete[] = [
                                'nome'  => $s['name'],
                                'preco' => number_format($s['price'], 2, ',', '.'),
                                'prazo' => $s['delivery_range']['max'] . ' dias úteis',
                                'icone' => 'fa-truck'
                            ];
                        }
                    }
                }
            }

            // 2. LÓGICA DO UBER (Baseada no SEDEX)
            $prefixo = substr($cepDestino, 0, 2);
            $regioesLocais = ['01', '02', '03', '04', '05', '06', '07', '08', '09'];

            if (in_array($prefixo, $regioesLocais)) {
                // Se conseguimos o preço do SEDEX, subtraímos 5.
                // Se a API falhou, usamos um valor fixo de segurança (ex: 20,00)
                $valorUber = $precoSedexBase ? ($precoSedexBase - 5) : 20.00;

                // Evita frete negativo ou de graça caso o SEDEX seja muito barato
                if ($valorUber < 10) $valorUber = 10.00;

                // Adiciona o Uber no INÍCIO da lista para dar destaque
                array_unshift($opcoesFrete, [
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
}
