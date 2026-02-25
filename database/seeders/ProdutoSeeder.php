<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Categorias
        $catVasos = Categoria::create(['nome' => 'Vasos', 'status' => 'ativa']);
        $catIluminacao = Categoria::create(['nome' => 'Iluminação', 'status' => 'ativa']);
        $catUtensilios = Categoria::create(['nome' => 'Utensílios', 'status' => 'ativa']);

        // 2. Criar Produtos
        Produto::create([
            'categoria_id' => $catVasos->id,
            'nome' => 'Vaso Cerâmica',
            'preco' => 149.90,
            'estoque' => 10,
            'codigo_referencia' => 'VASO-CER-01',
            'status' => 'ativo'
            // Não enviamos imagem aqui para ele usar a imagem padrão (vasomora.png) configurada no seu Blade
        ]);

        Produto::create([
            'categoria_id' => $catIluminacao->id,
            'nome' => 'Luminária Mesa',
            'preco' => 299.00,
            'estoque' => 5,
            'codigo_referencia' => 'LUM-MESA-01',
            'status' => 'ativo'
        ]);

        Produto::create([
            'categoria_id' => $catUtensilios->id,
            'nome' => 'Bandeja Madeira',
            'preco' => 89.90,
            'estoque' => 15,
            'codigo_referencia' => 'BAND-MAD-01',
            'status' => 'ativo'
        ]);
    }
}