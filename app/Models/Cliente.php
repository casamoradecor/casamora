<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nome', 'email', 'password', 'cpf', 'telefone', 'status'];
    protected $hidden = ['password', 'remember_token'];

    public function enderecos() { return $this->hasMany(Endereco::class); }
    public function pedidos() { return $this->hasMany(Pedido::class); }
}