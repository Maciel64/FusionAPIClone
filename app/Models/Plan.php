<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'name',
        'price',
        'description',
        'trial_period_days',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    // public function getPriceAttribute($value)
    // {
    //   return number_format($value / 100, 2, '.', '.');
    // }
}
