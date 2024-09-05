<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailedCharges extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $fillable = [
      'user_id',
      'year_reference',
      'month_reference',
      'failed_at',
      'attempts',
    ];

    protected $hidden = [
      'id',
      // 'user_id',
      'created_at',
      'updated_at',
      'deleted_at'
    ];


}
