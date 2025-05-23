<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Anotacao extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'descricao',
        'usuario_id',
    ];
}
