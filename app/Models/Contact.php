<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'model_id',
      'model_type',
      'type',
      'country_code',
      'area_code',
      'number',
    ];

    protected $hidden = [
      'id',
      'model_id',
      'model_type',
      'created_at',
      'updated_at',
    ];
}
