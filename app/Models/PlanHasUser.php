<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanHasUser extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'plan_id',
        'user_id',
        'active',
        'start_date',
    ];

    protected $appends = [
        'plan',
    ];

    protected $hidden = [
        'id',
        'plan_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function  getPlanAttribute()
    {
        return $this->plan()->first();
    }
}
