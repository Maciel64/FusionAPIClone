<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthAdviceHasUser extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'advice_code',
      'health_advice',
      'user_id',
    ];

    protected $hidden = [
      'id',
      'user_id',
      'created_at',
      'updated_at',
    ];
}
