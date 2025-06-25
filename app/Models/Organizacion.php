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
        'telefono',
    ];
    public function cuentas()
    {
        return $this->belongsToMany(Cuenta::class , 'cuentas_orgs', 'organizacion_id', 'cuenta_id');
    }
    
    public function cuentasOrgs()
    {
        return $this->hasMany(CuentasOrgs::class);
    }

    public function periodos()
    {
        return $this->hasMany(Periodo::class);
    }
}
