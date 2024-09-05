<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory, Uuids;

    // pegar apenas os orders_id que são do tipo appointments para efetuar o calculo corretamente
    protected $fillable = [
      'partner_id',
      'order_id',
      'status', // pending, paid, canceled
      'paid_at',
      'note',
      'amount',
      'discount',
      'total',
      'receipt_url', 
      'receipt_name',
      'updated_by',
    ];

    protected $hidden = [
      'id',
      'created_at',
      'updated_at',
    ];
    
}
