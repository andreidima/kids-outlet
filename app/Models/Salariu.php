<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salariu extends Model
{
    use HasFactory;

    protected $table = 'salarii';
    protected $guarded = [];

    /**
     * Returneaza produsul acetei operatii .
     */
    public function angajat()
    {
        return $this->belongsTo(Angajat::class);
    }
}
