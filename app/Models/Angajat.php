<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angajat extends Model
{
    use HasFactory;

    protected $table = 'angajati';
    protected $guarded = [];

    public function path()
    {
        return "/angajati/{$this->id}";
    }
}
