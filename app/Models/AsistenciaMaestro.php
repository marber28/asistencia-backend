<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaMaestro extends Model
{
    use HasFactory;
    protected $fillable = ['maestro_id', 'dia', 'leccion_id', 'estado'];
    public function maestro()
    {
        return $this->belongsTo(User::class);
    }
    public function leccion()
    {
        return $this->belongsTo(Leccion::class);
    }
}
