<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'visible',
        'password',
        'birthday',
        'phone',
        'enabled'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //obtener rol de usuario, no el atributo, sino el rol asignado
    public function role()
    {
        return $this->roles->pluck('name')->first();
    }

    public function anexos() {
        return $this->hasMany(Anexo::class);
    }

    public function anexosConAulas()
    {
        return $this->belongsToMany(Anexo::class, 'user_anexo_aula', 'user_id', 'anexo_id')
            ->withPivot('aula_id', 'user_id')
            ->join('aulas', 'aulas.id', '=', 'user_anexo_aula.aula_id')
            ->select(
                'anexos.*',
                'aulas.nombre as aula_nombre',
                'user_anexo_aula.aula_id',
            );
    }
}
