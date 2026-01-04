<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'direccion', 'fecha_creacion', 'user_id', 'logo', 'activo'];

    public function user()
    {
        return $this->belongsToMany(
            User::class,
            'user_anexo_aula',
            'anexo_id',
            'user_id'
        )
        ->withPivot('aula_id')
        ->whereHas('roles', function ($query) {
            $query->where('name', 'responsable');
        });
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
