<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
      'user_id', // customer
      'model_type',
      'model_id',
      'amount',
      'paid',
      'payment_method',
      'payment_at',
      'order_id',
      'order_code',
      'closed',
    ];

    protected $hidden = [
      'id',
      'user_id',
      // 'model_type',
      // 'model_id',
      // 'created_at',
      // 'updated_at',
    ];

    public function model()
    {
      return $this->morphTo();
    }

}
