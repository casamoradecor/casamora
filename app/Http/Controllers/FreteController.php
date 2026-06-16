<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FreteController extends Controller
{
    public function calcular(Request $request, MelhorEnvioService $melhorEnvio)
    {
        try {
            $produto = Produto::find($request->produto_id);

            if (!$produto) {
                return response()->json(['error' => 'Produto nao encontrado'], 404);
            }

            return response()->json(
                $melhorEnvio->calcularOpcoesParaProduto((string) $request->cep, $produto)
            );
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete do produto.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Falha ao calcular'], 500);
        }
    }

    public function calcularCarrinho(Request $request, MelhorEnvioService $melhorEnvio)
    {
        try {
            $carrinho = session()->get('carrinho', []);

            if (empty($carrinho)) {
                return response()->json(['error' => 'Carrinho vazio'], 400);
            }

            return response()->json(
                $melhorEnvio->calcularOpcoesParaCarrinho((string) $request->cep, $carrinho)
            );
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao calcular frete do checkout.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Falha ao calcular'], 500);
        }
    }
}
