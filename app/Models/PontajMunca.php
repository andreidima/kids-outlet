<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontajMunca extends Model
{
    use HasFactory;

    protected $table = 'pontaje_munca';
    protected $guarded = [];

    public function path()
    {
        return "/pontaje-munca/{$this->id}";
    }

    /**
     * Returneaza „angajatul” acetui „pontaj”.
     */
    public function angajat()
    {
        return $this->belongsTo(Angajat::class);
    }

    public function operatie()
    {
        return $this->belongsTo(ProdusOperatie::class, 'produs_operatie_id');
    }
}
