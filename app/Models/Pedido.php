<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id', 'endereco_id', 'valor_produtos', 'valor_frete',
        'valor_desconto', 'valor_total', 'status', 'payment_id',
        'codigo_externo', 'nome_entrega', 'cpf_entrega', 'cep', 'endereco','codigo_rastreio','servico_frete_id',
        'metodo_envio',
    ];

    protected function casts(): array
    {
        return [
            'cpf_entrega'  => 'encrypted',
            'nome_entrega' => 'encrypted',
            'endereco'     => 'encrypted',
            'cep'          => 'encrypted',
            'valor_produtos' => 'decimal:2',
            'valor_frete'    => 'decimal:2',
            'valor_desconto' => 'decimal:2',
            'valor_total'    => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
    public function itens() { return $this->hasMany(PedidoItem::class); }
    public function pagamento() { return $this->hasOne(Pagamento::class); }
    public function endereco() { return $this->belongsTo(Endereco::class); }
}
