<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('produtos')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            // 1. SALVAR NOVAS
            if ($request->has('novas_categorias')) {
                foreach ($request->novas_categorias as $dados) {
                    if (!empty($dados['nome'])) {
                        Categoria::create([
                            'nome'      => $dados['nome'],
                            'descricao' => $dados['descricao'] ?? '', // Salva a descrição aqui
                            'status'    => 'ativa'
                        ]);
                    }
                }
            }

            // 2. ATUALIZAR EXISTENTES
            if ($request->has('categorias_existentes')) {
                foreach ($request->categorias_existentes as $id => $dados) {
                    $cat = Categoria::find($id);
                    if ($cat) {
                        $cat->update([
                            'nome'      => $dados['nome'],
                            'descricao' => $dados['descricao'] ?? $cat->descricao // Mantém ou atualiza
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Alterações salvas!');
    }

    public function destroy($id)
    {
        $categoria = Categoria::withTrashed()->findOrFail($id);
        $categoria->forceDelete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoria excluída permanentemente!');
    }
}
