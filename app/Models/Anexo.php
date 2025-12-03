<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'direccion', 'fecha_creacion', 'user_id', 'logo', 'activo'];

    public function maestros()
    {
        return $this->belongsToMany(Maestro::class, 'anexo_maestro', 'anexo_id', 'maestro_id');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
