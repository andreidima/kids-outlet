<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NormaLucrata extends Model
{
    use HasFactory;

    protected $table = 'norme_lucrate';
    protected $guarded = [];

    public function path()
    {
        return "/norme-lucrate/{$this->id}";
    }

    /**
     * Returneaza produsul acetei operatii .
     */
    public function angajat()
    {
        return $this->belongsTo(Angajat::class);
    }

    /**
     * Returneaza produsul acetei operatii .
     */
    public function produs_operatie()
    {
        return $this->belongsTo(ProdusOperatie::class);
    }
}
