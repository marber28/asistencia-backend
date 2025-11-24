<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;
    protected $fillable = ['nombres', 'apellidos', 'email', 'activo'];
    public function asistencias()
    {
        return $this->hasMany(AsistenciaMaestro::class);
    }
    public function aulas()
    {
        return $this->belongsToMany(Aula::class, 'aula_maestro');
    }
}
