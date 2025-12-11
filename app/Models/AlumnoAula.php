<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoAula extends Model
{
    protected $fillable = ['alumno_id', 'aula_id', 'current'];

    public function alumno() {
        return $this->belongsTo(Alumno::class);
    }

    public function aula() {
        return $this->belongsTo(Aula::class);
    }
}
