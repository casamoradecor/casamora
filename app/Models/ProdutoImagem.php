<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoImagem extends Model
{
    protected $table = 'produto_imagens';
    protected $fillable = ['produto_id', 'caminho_imagem', 'is_principal'];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}