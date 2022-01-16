<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngajatRol extends Model
{
    use HasFactory;

    protected $table = 'angajati_roluri';
    protected $guarded = [];
}
