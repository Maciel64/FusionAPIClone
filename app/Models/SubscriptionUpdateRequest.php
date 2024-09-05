<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUpdateRequest extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'status',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'subscription_id',
        'plan_id',
        'created_at',
        'updated_at',
    ];
    public function subscription()
    {
      return $this->hasOne(Subscription::class)->first() ?? null;
    }

}
