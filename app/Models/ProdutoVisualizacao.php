<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProdutoVisualizacao extends Model
{
    protected $table = 'produto_visualizacoes';
    protected $fillable = ['produto_id', 'session_id'];
}
