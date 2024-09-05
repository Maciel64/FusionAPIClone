<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthAdvice extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'name',
      'initials'
    ];

    protected $hidden = [
      'id',
      'created_at',
      'updated_at',
    ];
}
