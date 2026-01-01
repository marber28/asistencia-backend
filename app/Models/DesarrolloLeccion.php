<?php

// app/Models/Desarrollo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesarrolloLeccion extends Model {

    protected $table = 'desarrollo_leccion';

    protected $fillable = [
        'leccion_id',
        'user_id',
        'anexo_id',
        'versiculo_memorizado',
        'ensenanza',
        'motivacion',
        'estrategias',
        'observaciones',
        'pdf'
    ];

    public function leccion(){
        return $this->belongsTo(Leccion::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function anexo() {
        return $this->belongsTo(Anexo::class);
    }
}
