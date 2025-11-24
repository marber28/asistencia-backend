<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'edad_min', 'edad_max', 'descripcion'];
    public function maestros()
    {
        return $this->belongsToMany(Maestro::class, 'aula_maestro');
    }
    public function asistencias()
    {
        return $this->hasMany(AsistenciaAlumno::class);
    }
}
