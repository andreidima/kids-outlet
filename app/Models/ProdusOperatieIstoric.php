<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdusOperatieIstoric extends Model
{
    use HasFactory;

    protected $table = 'produse_operatii_istoric';
    protected $guarded = [];

    // public function path()
    // {
    //     return "/produse-operatii/{$this->id}";
    // }
}
