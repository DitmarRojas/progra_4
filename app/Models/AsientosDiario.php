<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsientosDiario extends Model
{
    use HasFactory;

    protected $table = 'asientos_diarios';

    protected $fillable = [
        'nro_asiento',
        'monto_debe',
        'monto_haber',
        'descripcion',
        'transaccion_id',
        'cuenta_id',
    ];

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'transaccion_id');
    }
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
}
