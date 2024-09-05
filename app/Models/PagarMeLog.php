<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagarMeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'logged_user_name',
        'action',
        'request',
        'response'
    ];
}
