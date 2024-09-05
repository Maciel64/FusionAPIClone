<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'line_1',
      'line_2',
      'city',
      'state',
      'country',
      'neighborhood',
      'zip_code',
      'model_type',
      'model_id'
    ];

    protected $hidden = [
      'id',
      'created_at',
      'updated_at',
      'pivot',
      'model_type',
      'model_id',
    ];

}
