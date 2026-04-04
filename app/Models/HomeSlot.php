<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSlot extends Model
{
    protected $fillable = ['slot_number', 'categoria_id'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
