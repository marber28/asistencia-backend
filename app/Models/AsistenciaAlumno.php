<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaAlumno extends Model
{
    use HasFactory;
    protected $fillable = ['alumno_id', 'dia', 'estado', 'lista_imagen', 'observaciones'];

    protected function casts(): array
    {
        return [
            'dia' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
    
    // / Aula actual vía pivot
    public function aulaActual()
    {
        return $this->hasOneThrough(
            Aula::class,
            AlumnoAula::class,
            'alumno_id', // FK en alumno_aula
            'id',        // PK en aulas
            'alumno_id', // FK en asistencia
            'aula_id'    // FK en alumno_aula
        )->where('alumno_aulas.current', 1);
    }
}
