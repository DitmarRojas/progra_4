<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'nombre',
        'tipo',
        'ruta',
        'transaccion_id',
        'usuario_id',
    ];

    public function transacciones()
    {
        return $this->belongsTo(Transaccion::class, 'transaccion_id');
    }
    
    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
