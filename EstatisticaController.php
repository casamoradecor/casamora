<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Newsletter;
use App\Models\Visita;
use Illuminate\Support\Facades\DB;

class EstatisticaController extends Controller
{
    public function index()
    {
        // 1. Total de Usuários
        $totalUsuarios = User::count();

        // 2. Total da Newsletter (Usando o model que você enviou, pegando apenas os ativos)
        $totalNewsletter = Newsletter::where('active', true)->count();

        // 3. Acessos do Site (Puxando do sistema que acabamos de criar)
        $totalVisitas = Visita::count();

        // 4. Produtos Mais Vendidos (Considerando pedidos pagos ou enviados)
        $produtosMaisVendidos = DB::table('pedido_itens')
            ->join('pedidos', 'pedido_itens.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'pedido_itens.produto_id', '=', 'produtos.id')
            ->whereIn('pedidos.status', ['pago', 'enviado'])
            ->select('produtos.nome', DB::raw('SUM(pedido_itens.quantidade) as total_vendido'))
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->get();

        // Prepara as informações para o Chart.js
        $labelsProdutos = $produtosMaisVendidos->pluck('nome');
        $dadosProdutos = $produtosMaisVendidos->pluck('total_vendido');

        return view('admin.estatisticas.index', compact(
            'totalUsuarios',
            'totalNewsletter',
            'totalVisitas',
            'labelsProdutos',
            'dadosProdutos'
        ));
    }
}
