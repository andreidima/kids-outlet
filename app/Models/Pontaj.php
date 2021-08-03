<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pontaj extends Model
{
    use HasFactory;

    protected $table = 'pontaje';
    protected $guarded = [];

    public function path()
    {
        return "/pontaje/{$this->id}";
    }

    /**
     * Returneaza „angajatul” acetui „pontaj”.
     */
    public function angajat()
    {
        return $this->belongsTo(Angajat::class);
    }
}
