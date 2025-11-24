<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaAlumno extends Model
{
    use HasFactory;
    protected $fillable = ['alumno_id', 'aula_id', 'dia', 'estado', 'lista_imagen', 'observaciones', 'leccion_id'];
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }
    public function leccion()
    {
        return $this->belongsTo(Leccion::class);
    }
}
