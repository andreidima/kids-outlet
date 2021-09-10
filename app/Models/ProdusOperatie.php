<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdusOperatie extends Model
{
    use HasFactory;

    protected $table = 'produse_operatii';
    protected $guarded = [];

    public function path()
    {
        return "/produse-operatii/{$this->id}";
    }

    /**
     * Returneaza produsul acetei operatii .
     */
    public function produs()
    {
        return $this->belongsTo(Produs::class);
    }
}
