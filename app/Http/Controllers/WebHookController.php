<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function receberNotificacao(Request $request)
    {
        $paymentId = $this->obterPaymentId($request);
        $topic = (string) ($request->input('type') ?? $request->input('topic'));
        $requestId = (string) $request->header('x-request-id', '');

        Log::info('Webhook Mercado Pago recebido.', [
            'payment_id' => $paymentId,
            'topic' => $topic,
            'x_request_id' => $requestId,
        ]);

        if ($paymentId === '') {
            Log::warning('Webhook Mercado Pago sem data.id.', [
                'topic' => $topic,
                'x_request_id' => $requestId,
            ]);

            return response()->json(['message' => 'missing data.id'], 422);
        }

        if (!$this->validarAssinatura($request, $paymentId)) {
            Log::warning('Webhook Mercado Pago rejeitado por assinatura invalida.', [
                'payment_id' => $paymentId,
                'topic' => $topic,
                'x_request_id' => $requestId,
            ]);

            return response()->json(['message' => 'invalid signature'], 401);
        }

        if ($topic !== 'payment') {
            Log::warning('Webhook Mercado Pago fora de escopo.', [
                'payment_id' => $paymentId,
                'topic' => $topic,
                'x_request_id' => $requestId,
            ]);

            return response()->json(['status' => 'ignored'], 200);
        }

        $mpConfig = config('services.mercadopago');

        try {
            $response = Http::withToken($mpConfig['token'])
                ->acceptJson()
                ->connectTimeout($mpConfig['connect_timeout'])
                ->timeout($mpConfig['timeout'])
                ->get(rtrim($mpConfig['base_url'], '/') . "/v1/payments/{$paymentId}");

            if ($response->failed()) {
                Log::error('Falha ao consultar pagamento no Mercado Pago.', [
                    'payment_id' => $paymentId,
                    'topic' => $topic,
                    'x_request_id' => $requestId,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return response()->json(['message' => 'payment lookup failed'], 502);
            }

            $dadosMp = $response->json();
            $pedidoId = (string) ($dadosMp['external_reference'] ?? '');
            $statusMp = (string) ($dadosMp['status'] ?? '');

            if ($pedidoId === '' || $statusMp === '') {
                Log::warning('Retorno de pagamento sem campos obrigatorios.', [
                    'payment_id' => $paymentId,
                    'topic' => $topic,
                    'x_request_id' => $requestId,
                    'response' => $dadosMp,
                ]);

                return response()->json(['message' => 'invalid payment payload'], 422);
            }

            $pedido = Pedido::find($pedidoId);

            if (!$pedido) {
                Log::warning('Webhook Mercado Pago com pedido nao encontrado.', [
                    'payment_id' => $paymentId,
                    'pedido_id' => $pedidoId,
                    'topic' => $topic,
                    'x_request_id' => $requestId,
                ]);

                return response()->json(['message' => 'order not found'], 404);
            }

            if ($statusMp === 'approved') {
                $pedido->update(['status' => 'pago']);
            } elseif ($statusMp === 'rejected') {
                $pedido->update(['status' => 'cancelado']);
            }

            $pagamento = Pagamento::where('pedido_id', $pedido->id)->first();

            if ($pagamento) {
                $jsonHigienizado = [
                    'id' => $dadosMp['id'] ?? null,
                    'status_detail' => $dadosMp['status_detail'] ?? null,
                    'payment_method_id' => $dadosMp['payment_method_id'] ?? null,
                    'payment_type_id' => $dadosMp['payment_type_id'] ?? null,
                    'currency_id' => $dadosMp['currency_id'] ?? null,
                    'installments' => $dadosMp['installments'] ?? null,
                    'date_approved' => $dadosMp['date_approved'] ?? null,
                ];

                $pagamento->update([
                    'status' => $statusMp,
                    'transaction_id' => $paymentId,
                    'valor_pago' => $dadosMp['transaction_amount'] ?? 0,
                    'json_retorno' => $jsonHigienizado,
                ]);
            }

            Log::info('Webhook Mercado Pago processado com sucesso.', [
                'payment_id' => $paymentId,
                'pedido_id' => $pedido->id,
                'status' => $statusMp,
                'x_request_id' => $requestId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado no webhook Mercado Pago.', [
                'payment_id' => $paymentId,
                'topic' => $topic,
                'x_request_id' => $requestId,
                'error_type' => get_class($e),
            ]);

            return response()->json(['message' => 'internal error'], 500);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function obterPaymentId(Request $request): string
    {
        return (string) (
            $request->query('data.id')
            ?? data_get($request->all(), 'data.id')
            ?? $request->input('data_id')
            ?? $request->input('id')
            ?? ''
        );
    }

    private function validarAssinatura(Request $request, string $paymentId): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        $signature = (string) $request->header('x-signature', '');
        $requestId = (string) $request->header('x-request-id', '');

        $secret = preg_replace('/[^a-zA-Z0-9]/', '', (string) config('services.mercadopago.webhook_secret'));

        if ($signature === '' || $requestId === '' || $secret === '') {
            return false;
        }

        $ts = '';
        $v1s = [];

        $parts = explode(',', $signature);
        foreach ($parts as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) === 2) {
                if ($kv[0] === 'ts') {
                    $ts = $kv[1];
                } elseif ($kv[0] === 'v1') {
                    $v1s[] = $kv[1];
                }
            }
        }

        if ($ts === '' || empty($v1s)) {
            return false;
        }

        $manifest = "id:{$paymentId};request-id:{$requestId};ts:{$ts};";
        $calculated = hash_hmac('sha256', $manifest, $secret);

        foreach ($v1s as $v1) {
            if (hash_equals($calculated, $v1)) {
                return true;
            }
        }

        Log::warning('Assinatura MP Invalida.', [
            'manifesto' => $manifest,
            'recebidos' => $v1s
        ]);

        return false;
    }
}
