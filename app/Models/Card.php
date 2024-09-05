<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory, Uuids;

    protected $table = 'cards';

    protected $fillable = [
      'user_id',
      'customer_id', // id do customer no pagarme
      'address_id', // id do endereço no pagarme
      'card_id', // id do cartão no pagarme
      'first_six_digits',
      'last_four_digits',
      'brand',
      'holder_name',
      'holder_document',
      'exp_month',
      'exp_year',
      'status',
      'type',
      'label',
      'card_token',
      'is_default',
    ];

    protected $hidden = [
      'id',
      'user',
      // 'user_id',
      // 'customer_id',
      // 'address_id',
      // 'card_token',
      // 'card_id',
      'created_at',
      'updated_at',
    ];

    protected $appends = [
      'user_uuid',
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function getUserUuidAttribute()
    {
      return $this->user->uuid;
    }

}
