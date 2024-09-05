<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserVerifyCode extends Model
{
    use HasFactory, Uuids;


    protected $fillable = [
      'user_id',
      'code',
      'email',
    ];

    protected $hidden = [
      'id',
      'user_id',
      'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getExpiresAtAttribute($value)
    {
      if($value) 
        return Carbon::parse($value)->diffForHumans();
      return null;
    }

}
