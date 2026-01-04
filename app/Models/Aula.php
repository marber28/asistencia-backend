<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'edad_min', 'edad_max', 'descripcion'];

    public function alumnos() {
        return $this->belongsToMany(Alumno::class, 'alumno_aulas', 'aula_id', 'alumno_id');
    }

    public function maestros()
    {
        return $this->belongsToMany(UserAnexoAula::class, 'aula_maestro');
    }
    public function asistencias()
    {
        return $this->hasMany(AsistenciaAlumno::class);
    }
}
