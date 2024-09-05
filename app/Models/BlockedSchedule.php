<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedSchedule extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'room_id',
        'time_init',
        'time_end',
      ];
  
      protected $hidden = [
        'id',
      ];
}
