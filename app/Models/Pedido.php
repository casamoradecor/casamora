<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'endereco_id', 'valor_produtos', 'valor_frete', 'valor_desconto', 'valor_total', 'status', 'payment_id', 'codigo_externo'];

    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function itens() { return $this->hasMany(PedidoItem::class); }
    public function pagamento() { return $this->hasOne(Pagamento::class); }
    public function endereco() { return $this->belongsTo(Endereco::class); }
}