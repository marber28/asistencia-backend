<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;
    protected $fillable = ['nombres', 'apellidos', 'email', 'telefono', 'fecha_nacimiento', 'activo', 'anexo_id'];
    public function asistencias()
    {
        return $this->hasMany(AsistenciaMaestro::class);
    }

    //relacion con tabla anexo_maestro_aula para obtener el aula filtrado por el maestro actual
    public function aulas() {
        return $this->belongsToMany(Aula::class, 'anexo_maestro_aula', 'maestro_id', 'aula_id');
    }

    //relacion con tabla anexo_maestro_aula para obtener el anexo
    public function anexos() {
        return $this->belongsToMany(Anexo::class, 'anexo_maestro_aula', 'maestro_id', 'anexo_id')->wherePivot('current', true);
    }
}
