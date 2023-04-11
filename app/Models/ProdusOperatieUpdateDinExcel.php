<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdusOperatieUpdateDinExcel extends Model
{
    use HasFactory;

    protected $table = 'produse_operatii_update_din_excel';
    protected $guarded = [];

    // public function path()
    // {
    //     return "/produse-operatii/{$this->id}";
    // }

    public function produsOperatie()
    {
        return $this->belongsTo(ProdusOperatie::class, 'produs_id', 'produs_id')
            ->where('numar_de_faza', $this->numar_de_faza);
    }
}
