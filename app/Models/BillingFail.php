<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingFail extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'user_id',
        'reference_date',
        'reference_type', // monthly, yearly - settings.billing_type
        'attempts', // quantidade de tentativas de cobrança
        'status', // failed, defaulter, paid
        'description',
        'model_type',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
      'attempts_list',
    ];

    public function attempts()
    {
      return $this->hasMany(BillingFailAttempt::class);
    }

    public function getAttemptsListAttribute()
    {
      return $this->attempts()->get();
    }
}
