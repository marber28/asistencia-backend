<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnexoMaestroAula extends Model
{
    use HasFactory;

    protected $table = 'anexo_maestro_aula';
    protected $fillable = ['anexo_id', 'maestro_id', 'aula_id', 'current'];

    //relacion para obtener el anexo
    public function anexo()
    {
        return $this->belongsTo(Anexo::class);
    }

    //relacion para obtener el maestro
    public function maestro()
    {
        return $this->belongsTo(Maestro::class);
    }
}
