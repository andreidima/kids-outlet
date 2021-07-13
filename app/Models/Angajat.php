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

    public function pontaj()
    {
        return $this->HasMany('App\Models\Pontaj', 'angajat_id');
    }

    public function pontaj_azi()
    {
        return $this->hasOne('App\Models\Pontaj', 'angajat_id')->where('data', \Carbon\Carbon::today());
    }
}
