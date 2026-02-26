<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class CarrinhoController extends Controller
{
    // Novo método: Retorna os dados atuais do carrinho para o JS
    public function listar()
    {
        $carrinho = session()->get('carrinho', []);
        return response()->json([
            'itens' => $carrinho,
            'total' => $this->calcularTotal($carrinho)
        ]);
    }

    // Método adicionar ajustado
    public function adicionar(Request $request)
    {
        $produto = Produto::findOrFail($request->produto_id);
        $carrinho = session()->get('carrinho', []);

        if(isset($carrinho[$produto->id])) {
            $carrinho[$produto->id]['quantidade']++;
        } else {
            $carrinho[$produto->id] = [
                "id" => $produto->id,
                "nome" => $produto->nome,
                "quantidade" => 1,
                "preco" => $produto->preco,
                "imagem" => $produto->imagem
            ];
        }

        session()->put('carrinho', $carrinho);
        
        // Agora devolvemos o carrinho atualizado
        return response()->json([
            'success' => true,
            'itens' => $carrinho,
            'total' => $this->calcularTotal($carrinho)
        ]);
    }
    public function diminuir(Request $request)
{
    $carrinho = session()->get('carrinho', []);
    $id = $request->produto_id;

    if(isset($carrinho[$id])) {
        if($carrinho[$id]['quantidade'] > 1) {
            $carrinho[$id]['quantidade']--;
        } else {
            unset($carrinho[$id]); // Remove se for o último
        }
        session()->put('carrinho', $carrinho);
    }

    return response()->json([
        'success' => true,
        'itens' => $carrinho,
        'total' => $this->calcularTotal($carrinho)
    ]);
}

    // Função auxiliar para somar tudo
    private function calcularTotal($carrinho)
    {
        $total = 0;
        foreach($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
    public function checkout()
{
    $carrinho = session()->get('carrinho', []);
    
    // Se o carrinho estiver vazio, manda de volta pra home
    if(empty($carrinho)) {
        return redirect()->route('home');
    }

    $total = $this->calcularTotal($carrinho);
    
    return view('checkout', compact('carrinho', 'total'));
}
}