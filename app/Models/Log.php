<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'vista',
        'detalle',
        'type',
        //'user_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'type' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($log) {
            if (\Auth::check() && empty($log->user_id)) {
                $log->user_id = \Auth::id();
            }
        });
    }
}
