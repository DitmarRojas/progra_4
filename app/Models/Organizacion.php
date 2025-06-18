<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';
    
    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
    ];
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class);
    }
    public function periodos()
    {
        return $this->hasMany(Periodo::class);
    }
}
