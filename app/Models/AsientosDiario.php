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
        'moto_credito',
        'monto_debito',
        'descripcion',
        'transaccion_id',
        'cuenta_id',
    ];

    public function transacciones()
    {
        return $this->belongsTo(Transaccion::class, 'transaccion_id');
    }
    
    public function cuentas()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
}
