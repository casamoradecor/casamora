<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppablePoint extends Model
{
    protected $fillable = ['produto_id', 'x_pos', 'y_pos'];

    public function produto() {
        return $this->belongsTo(Produto::class);
    }
}
