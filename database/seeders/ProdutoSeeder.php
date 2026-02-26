<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria uma categoria padrão caso não exista
        // Se você tiver um Model 'Categoria', pode usar Categoria::firstOrCreate...
        // Aqui vamos direto na tabela para garantir:
        $categoriaId = DB::table('categorias')->insertGetId([
            'nome' => 'Coleção Principal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $produtos = [
            [
                'nome' => 'Vaso Morá Minimalist',
                'preco' => 189.90,
                'descricao' => 'Vaso de cerâmica artesanal com acabamento fosco.',
                'imagem' => 'assets/vasomora.png',
                'categoria_id' => $categoriaId // Adicionado
            ],
            [
                'nome' => 'Vaso Eclipse Black',
                'preco' => 245.00,
                'descricao' => 'Design moderno em preto profundo para ambientes luxuosos.',
                'imagem' => 'assets/vasomora.png',
                'categoria_id' => $categoriaId // Adicionado
            ],
            [
                'nome' => 'Centro de Mesa Organic',
                'preco' => 320.00,
                'descricao' => 'Peça exclusiva com formas orgânicas inspiradas na natureza.',
                'imagem' => 'assets/vasomora.png',
                'categoria_id' => $categoriaId // Adicionado
            ]
        ];

        foreach ($produtos as $p) {
            Produto::create($p);
        }
    }
}