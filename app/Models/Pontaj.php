<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pontaj extends Model
{
    use HasFactory;

    protected $table = 'pontaj';
    protected $guarded = [];

    public function path()
    {
        return "/pontaj/{$this->id}";
    }
}
