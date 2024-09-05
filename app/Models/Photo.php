<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'name',      
      'url',
      'model_type',
      'model_id'
    ];

    protected $hidden = [
      'id',
      'model_type',
      'model_id',
      'created_at',
      'updated_at',
      'pivot',
    ];
}
