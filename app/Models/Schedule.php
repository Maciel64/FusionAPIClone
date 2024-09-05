<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'user_id',
      'name'
    ];

    protected $hidden = [
      'id',
      'user_id',
      'created_at',
      'updated_at',
    ];
}
