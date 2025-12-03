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
    public function aulas()
    {
        return $this->belongsToMany(Aula::class, 'aula_maestro');
    }

    // Relación muchos a muchos con Anexo
    public function anexos()
    {
        return $this->belongsToMany(Anexo::class, 'anexo_maestro');
    }
}
