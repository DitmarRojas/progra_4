<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

    protected $table = 'cuentas';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'descripcion',
        'nivel',
        'estado',
    ];

    public function organizaciones()
    {
        return $this->belongsToMany(Organizacion::class, 'cuentas_orgs', 'cuenta_id', 'organizacion_id');
    }

    public function cuentasOrgs()
    {
        return $this->hasMany(CuentasOrgs::class);
    }
    
    public function asientosDiarios()
    {
        return $this->hasMany(AsientosDiario::class);
    }
}
