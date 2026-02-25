<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'descricao',
        'status'
    ];

    /**
     * Relacionamento: Uma categoria possui muitos produtos.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}