<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnexoAula extends Model
{
    use HasFactory;

    protected $table = 'user_anexo_aula';
    protected $fillable = ['user_id', 'anexo_id', 'aula_id'];

    //relacion para obtener el aula
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    //relacion para obtener el user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
