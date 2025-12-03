<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $fillable = ['nombres', 'apellidos', 'fecha_nacimiento', 'anexo_id', 'foto'];
    public function asistencias()
    {
        return $this->hasMany(AsistenciaAlumno::class);
    }

    public function anexo()
    {
        return $this->belongsTo(Anexo::class);
    }
}
