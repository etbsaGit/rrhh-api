<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bay extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cliente',
        'maquina',
        'descripcion',
        'status',
        'tecnico_id',
        'sucursal_id',
        'linea_id'
    ];

    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'linea_id');
    }
}
