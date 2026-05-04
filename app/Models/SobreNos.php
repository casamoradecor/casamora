<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SobreNos extends Model
{
    use HasFactory;

    protected $table = 'sobre_nos';

    protected $fillable = ['titulo_header', 'texto_1', 'imagem_1', 'texto_2', 'imagem_2'];
}
