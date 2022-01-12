<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produs extends Model
{
    use HasFactory;

    protected $table = 'produse';
    protected $guarded = [];

    public function path()
    {
        return "/produse/{$this->id}";
    }

    /**
     * Get all of the produse_operatii for the Produs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function produse_operatii()
    {
        return $this->hasMany(ProdusOperatie::class, 'produs_id', 'id');
    }
}
