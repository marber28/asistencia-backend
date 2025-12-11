<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $fillable = ['nombres', 'apellidos', 'fecha_nacimiento', 'genero', 'anexo_id', 'foto'];
    public function asistencias()
    {
        return $this->hasMany(AsistenciaAlumno::class);
    }

    public function anexo()
    {
        return $this->belongsTo(Anexo::class);
    }

    public function aulas()
    {
        return $this->hasMany(AlumnoAula::class)->orderByDesc('current');
    }

    public function aulaActual()
    {
        return $this->hasOne(AlumnoAula::class)->where('current', true);
    }
}
