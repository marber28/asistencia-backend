<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leccion extends Model
{
    use HasFactory;
    protected $table = 'lecciones';
    protected $fillable = ['titulo', 'fecha', 'versiculo', 'archivo_pdf'];
    public function asistenciasAlumnos()
    {
        return $this->hasMany(AsistenciaAlumno::class);
    }
    public function asistenciasMaestros()
    {
        return $this->hasMany(AsistenciaMaestro::class);
    }
}
