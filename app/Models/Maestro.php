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

    //relacion con tabla anexo_maestro_aula para obtener el anexo
    public function anexos()
    {
        return $this->belongsToMany(Anexo::class, 'anexo_maestro_aula', 'maestro_id', 'anexo_id')
            ->withPivot('aula_id', 'current');
    }

    public function anexosConAulas()
    {
        return $this->belongsToMany(Anexo::class, 'anexo_maestro_aula', 'maestro_id', 'anexo_id')
            ->withPivot('aula_id', 'current')
            ->join('aulas', 'aulas.id', '=', 'anexo_maestro_aula.aula_id')
            ->select(
                'anexos.*',
                'aulas.nombre as aula_nombre',
                'anexo_maestro_aula.aula_id',
                'anexo_maestro_aula.current'
            );
    }

    public function getAulaAttribute()
    {
        return Aula::find($this->pivot->aula_id);
    }
}
