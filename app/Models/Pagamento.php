<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $fillable = ['pedido_id', 'metodo', 'status', 'valor_pago', 'transaction_id', 'json_retorno'];
    protected $casts = ['json_retorno' => 'array']; // Converte JSON do banco para array PHP automaticamente

    public function pedido() { return $this->belongsTo(Pedido::class); }
}