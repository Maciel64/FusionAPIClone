<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoworkingOpeningHours extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'coworking_id',
      'day_of_week',
      'opening',
      'closing',
    ];

    protected $casts = [
      'opening' => 'datetime:H:i',
      'closing' => 'datetime:H:i',
    ];

    protected $hidden = [
      'id',
      'coworking_id',
      'created_at',
      'updated_at',
    ];
}
