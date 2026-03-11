<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    // Define quais campos podem ser preenchidos via formulário/array
    protected $fillable = ['nome', 'preco', 'imagem', 'categoria_id','estoque','lancamento','descricao'];

    /**
     * Relacionamento: Um produto pertence a uma categoria.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relacionamento: Um produto pode ter várias imagens (Galeria).
     */
    public function imagens()
    {
        return $this->hasMany(ProdutoImagem::class);
    }

    /**
     * Relacionamento: Um produto pode estar em muitos itens de pedidos.
     */
    public function itensPedido()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
