<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avans extends Model
{
    use HasFactory;

    protected $table = 'avansuri';
    protected $guarded = [];

    /**
     * Returneaza produsul acetei operatii .
     */
    public function angajat()
    {
        return $this->belongsTo(Angajat::class);
    }
}
