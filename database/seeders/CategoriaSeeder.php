<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\Categoria::insert([
        ['id' => 1, 'nome' => 'vasos'],
        ['id' => 2, 'nome' => 'utensílios'],
        ['id' => 3, 'nome' => 'decorações'],
    ]);
}
}
