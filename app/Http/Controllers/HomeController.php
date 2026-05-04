<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use App\Models\HomeConfig;
use App\Models\ShoppablePoint;
use App\Models\HomeSlot;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('lancamento', true)
            ->latest()
            ->get();

        $categorias = Categoria::where('status', 'ativa')->get();

        $shoppablePoints = ShoppablePoint::with('produto')->get();

        $slots = HomeSlot::with('categoria')->get()->keyBy('slot_number');
        $homeConfig = HomeConfig::first() ?? new HomeConfig();

        return view('home', compact('produtos', 'categorias', 'shoppablePoints', 'slots','homeConfig'));
    }

    public function shop()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }
}
