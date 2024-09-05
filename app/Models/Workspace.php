<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
  use HasFactory, Uuids, SoftDeletes;

  protected $fillable = [
    'name',
    'user_id',
  ];

  protected $hidden = [
    'id',
    'user_id',
    'deleted_at',
    'created_at',
    'updated_at',
  ];
}
