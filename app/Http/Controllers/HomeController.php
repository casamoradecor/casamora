<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('lancamento', true)
            ->latest() 
            ->get();

        // Mantemos a busca de categorias ativas
        $categorias = Categoria::where('status', 'ativa')->get();

        return view('home', compact('produtos', 'categorias'));
    }
}