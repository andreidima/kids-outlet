<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngajatPontator extends Model
{
    use HasFactory;

    protected $table = 'angajati_pontatori';
    protected $guarded = [];

    public function angajat()
    {
        return $this->hasOne('App\Models\Angajat', 'pontator_angajat_id');
    }

    public function angajati_de_pontat()
    {
        return $this->HasMany('App\Models\Angajat', 'angajat_id');
    }

    public function angajat_pontatori() // returneaza pontatorii unui angajat
    {
        return $this->HasMany('App\Models\Angajat', 'pontator_angajat_id');
    }
}
