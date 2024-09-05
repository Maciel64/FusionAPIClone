<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $fillable = [
      'user_id',
      'plan_id',
      'pagarme_card_id',
      'price',
      'status',
    ];

}
