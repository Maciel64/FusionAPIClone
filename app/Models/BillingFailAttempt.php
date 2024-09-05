<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingFailAttempt extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'billing_fail_id',
        'status', // failed, defaulter, paid
    ];

    protected $hidden = [
      'id',
      'updated_at',
    ];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}
