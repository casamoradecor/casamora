<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Endereco extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'cliente_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento'];

    public function cliente() { return $this->belongsTo(Cliente::class); }
}
