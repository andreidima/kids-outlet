<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variabila extends Model
{
    use HasFactory;

    protected $table = 'variabile';
    protected $guarded = [];

    public function path()
    {
        return "/variabile/{$this->id}";
    }
}
