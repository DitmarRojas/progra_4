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
        'estado',
        'organizacion_id',
    ];

    public function organizaciones()
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }
    
    public function asientosDiarios()
    {
        return $this->hasMany(AsientosDiario::class);
    }
}
