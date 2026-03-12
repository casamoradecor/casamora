<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $fillable = [
        'cliente_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento'];

    public function cliente() { return $this->belongsTo(Cliente::class); }
}
