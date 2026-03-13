<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pedido;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function receberNotificacao(Request $request)
    {
        // 1. O Mercado Pago envia o ID da notificação no corpo ou na query string
        $id = $request->input('data_id') ?? $request->input('id');
        $topic = $request->input('type') ?? $request->input('topic');

        // Só nos interessam notificações de pagamento
        if ($topic == 'payment') {

            // 2. Consultamos o Mercado Pago para saber o status REAL desse pagamento
            $response = Http::withToken(env('MERCADOPAGO_ACCESS_TOKEN'))
                ->get("https://api.mercadopago.com/v1/payments/{$id}");

            if ($response->successful()) {
                $dadosMp = $response->json();
                $pedidoId = $dadosMp['external_reference']; // O ID que enviamos no checkout
                $statusMp = $dadosMp['status']; // 'approved', 'pending', etc.

                $pedido = Pedido::find($pedidoId);

                if ($pedido) {
                    // 3. Atualiza o Pedido
                    if ($statusMp == 'approved') {
                        $pedido->update(['status' => 'pago']);
                    } elseif ($statusMp == 'rejected') {
                        $pedido->update(['status' => 'cancelado']);
                    }

                    // 4. Atualiza a sua tabela de Pagamentos
                    $pagamento = Pagamento::where('pedido_id', $pedido->id)->first();
                    if ($pagamento) {
                        $pagamento->update([
                            'status' => $statusMp,
                            'transaction_id' => $id,
                            'valor_pago' => $dadosMp['transaction_amount'] ?? 0,
                            'json_retorno' => $dadosMp // Guarda tudo para auditoria
                        ]);
                    }
                }
            }
        }
        return response()->json(['status' => 'ok'], 200);
    }
}
