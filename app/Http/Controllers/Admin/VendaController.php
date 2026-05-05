<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    /**
     * Lista apenas os pedidos com status 'pago' para a gestão de envios.
     */
    public function index()
    {
        $pedidos = Pedido::with('cliente')
            ->latest()
            ->get();

        return view('admin.pedidos.index', compact('pedidos'));
    }

    /**
     * Exibe os detalhes de uma venda específica (itens, endereço e financeiro).
     */
    public function show($id)
    {
        // Carrega o pedido com todos os detalhes para o despacho [cite: 324, 327]
        $pedido = Pedido::with(['cliente', 'itens.produto', 'endereco'])->findOrFail($id);

        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Marca o pedido como enviado e registra o código de logística.
     */
    public function marcarComoEnviado(Request $request, $id)
    {
        $request->validate([
            'codigo_rastreio' => 'required|string|max:255'
        ]);

        $pedido = Pedido::with('cliente')->findOrFail($id);

        $pedido->update([
            'status' => 'enviado',
            'codigo_rastreio' => $request->codigo_rastreio
        ]);

        if ($pedido->cliente && $pedido->cliente->email) {
            \Illuminate\Support\Facades\Mail::to($pedido->cliente->email)
                ->send(new \App\Mail\PedidoEnviadoMail($pedido));
        }

        return redirect()->route('admin.vendas.index')
            ->with('sucesso', "Pedido #{$id} marcado como enviado e e-mail disparado!");
    }
    public function emitirEtiqueta(Request $request, $id, \App\Services\MelhorEnvioService $meService)
    {
        $pedido = \App\Models\Pedido::findOrFail($id);

        $carrinho = $meService->adicionarAoCarrinho($pedido);

        if (isset($carrinho['id'])) {
            $orderId = $carrinho['id'];
            $meService->finalizarCompra($orderId);
            sleep(2);
            $etiqueta = $meService->gerarEtiqueta($orderId);

            if (isset($etiqueta['url'])) {
                return redirect()->back()
                    ->with('sucesso', 'Etiqueta gerada com sucesso! Você já pode baixar o PDF.')
                    ->with('etiqueta_url', $etiqueta['url']);
            }
        }

        return redirect()->back()->with('erro', 'Falha ao processar etiqueta. Verifique seu saldo ou os dados de endereço.');
    }
}
