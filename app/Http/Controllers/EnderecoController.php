<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnderecoController extends Controller
{
    public function index()
    {
        $enderecos = Endereco::where('cliente_id', Auth::id())->latest()->get();
        $enderecos = $enderecos->unique(function ($item) {
            return $item['cep'] . $item['numero'];
        });

        return view('enderecos.index', compact('enderecos'));
    }

    public function create()
    {
        return view('enderecos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cep' => 'required', 'logradouro' => 'required', 'numero' => 'required',
            'bairro' => 'required', 'cidade' => 'required', 'estado' => 'required',
        ]);

        Endereco::create([
            'cliente_id' => Auth::id(),
            'cep' => preg_replace('/\D/', '', $request->cep),
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'complemento' => $request->complemento,
        ]);

        return redirect()->route('enderecos.index')->with('sucesso', 'ENDEREÇO CADASTRADO!');
    }

    public function edit($id)
    {
        $endereco = Endereco::where('cliente_id', Auth::id())->findOrFail($id);
        return view('enderecos.edit', compact('endereco'));
    }

    public function update(Request $request, $id)
    {
        $endereco = Endereco::where('cliente_id', Auth::id())->findOrFail($id);

        $endereco->update($request->all());

        return redirect()->route('enderecos.index')->with('sucesso', 'ENDEREÇO ATUALIZADO!');
    }

    public function destroy($id)
    {
        $endereco = Endereco::where('cliente_id', Auth::id())->findOrFail($id);
        $endereco->delete();

        return redirect()->route('enderecos.index')->with('sucesso', 'ENDEREÇO EXCLUÍDO!');
    }
}
