<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Pega os 10 últimos produtos ativos para o carrossel
        $produtos = Produto::where('status', 'ativo')
            ->latest()
            ->take(10)
            ->get();

        // Pega as categorias ativas para a seção "Compre por Categoria"
        $categorias = Categoria::where('status', 'ativa')->get();

        // O nome 'home' deve ser o nome do seu arquivo Blade (ex: home.blade.php)
        return view('home', compact('produtos', 'categorias'));
    }
}