<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Exibe o resumo da conta do usuário
     */
    public function index()
    {
        $ultimoPedido = Pedido::where('cliente_id', Auth::id())
            ->latest()
            ->first();
        return view('dashboard', compact('ultimoPedido'));
    }
}
