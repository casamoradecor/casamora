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
        $pedidos = Pedido::where('status', 'pago')
            ->with('cliente')
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

        $pedido = Pedido::findOrFail($id);
        $pedido->update([
            'status' => 'enviado',
            'codigo_rastreio' => $request->codigo_rastreio
        ]);

        return redirect()->route('admin.vendas.index')
            ->with('sucesso', "Pedido #{$id} marcado como enviado!");
    }
}
