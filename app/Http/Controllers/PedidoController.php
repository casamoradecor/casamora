<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::where('cliente_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pedidos.index', compact('pedidos'));
    }

    // Detalhes de um pedido específico
    public function show($id)
    {
        $pedido = Pedido::with(['itens.produto', 'pagamento'])
            ->where('cliente_id', Auth::id())
            ->findOrFail($id);

        return view('pedidos.show', compact('pedido'));
    }
}
