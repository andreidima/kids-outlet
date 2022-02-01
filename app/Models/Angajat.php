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

    public function roluri()
    {
        return $this->HasMany('App\Models\AngajatRol');
    }

    public function hasRol($rol)
        {
            if ($this->roluri()->where('rol', $rol)->first()) {
                return true;
            }
            // else
            return false;
        }

    public function pontaj()
    {
        return $this->HasMany('App\Models\Pontaj', 'angajat_id');
    }

    public function pontaj_azi()
    {
        return $this->hasOne('App\Models\Pontaj', 'angajat_id')->where('data', \Carbon\Carbon::today());
    }

    public function norme_lucrate()
    {
        return $this->HasMany('App\Models\NormaLucrata', 'angajat_id');
    }

    public function angajati_de_pontat()
    {
        // return $this->HasMany('App\Models\Angajat', 'angajat_id');
        return $this->belongsToMany(Angajat::class, 'angajati_pontatori', 'pontator_angajat_id', 'angajat_id');
    }
}
