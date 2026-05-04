<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HomeConfig extends Model {
    protected $fillable = ['hero_text'];
}
