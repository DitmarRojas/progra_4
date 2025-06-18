<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones';

    protected $fillable = [
        'fecha_transaccion',
        'descripcion',
        'num_referencia',
        'tipo_transaccion',
        'estado',
        'usuario_id',
    ];

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
    
    public function asientosDiarios()
    {
        return $this->hasMany(AsientosDiario::class);
    }
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }
}
